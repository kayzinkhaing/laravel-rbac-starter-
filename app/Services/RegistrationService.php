<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Contracts\UserInterface;
use App\Events\UserRegistered;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Redis;

class RegistrationService extends Common
{
    protected $userRepository;

    protected int $verificationExpiry = 60; // minutes

    protected int $resendCooldown = 45; // seconds

    public function __construct(UserInterface $userRepository)
    {
        parent::__construct($userRepository);

        $this->userRepository = $userRepository;
    }


    /**
     * Store pending WEB registration in Redis.
     * The account is not created until OTP verification succeeds.
     */
    public function storeWebPendingRegistration(array $data): void
    {
        $email = strtolower(trim($data['email']));

        $otp = $this->generateCode();

        $pending = [
            'name' => $data['name'],
            'email' => $email,
            'password' => Hash::make($data['password']),
            'otp_hash' => Hash::make($otp),
        ];

        Redis::setex(
            "register:web:{$email}",
            $this->verificationExpiry * 60,
            json_encode($pending)
        );

        Redis::del("register:web:attempts:{$email}");
        Redis::del("register:web:resend:{$email}");

        event(new \App\Events\UserRegistered($email, $otp));
    }

    /**
     * Verify WEB registration OTP and create the verified account.
     */
    public function verifyWebCode(string $email, string $code): ?User
    {
        $email = strtolower(trim($email));

        $key = "register:web:{$email}";
        $attemptKey = "register:web:attempts:{$email}";

        $stored = Redis::get($key);

        if (! $stored) {
            return null;
        }

        $attempts = Redis::incr($attemptKey);

        if ($attempts === 1) {
            Redis::expire($attemptKey, $this->verificationExpiry * 60);
        }

        if ($attempts > 5) {
            Redis::del($key);
            Redis::del($attemptKey);

            return null;
        }

        $data = json_decode($stored, true);

        if (
            ! $data ||
            empty($data['otp_hash']) ||
            ! Hash::check($code, $data['otp_hash'])
        ) {
            return null;
        }

        // Prevent duplicate account creation if another registration
        // claimed the email while this OTP was pending.
        if (User::where('email', $email)->exists()) {
            Redis::del($key);
            Redis::del($attemptKey);

            return null;
        }

        // Create the account only after successful OTP verification.
        $user = User::create([
            'name' => $data['name'],
            'email' => $email,
            'password' => $data['password'],
        ]);

        // email_verified_at is deliberately set after OTP verification.
        $user->forceFill([
            'email_verified_at' => now(),
        ])->save();

        Redis::del($key);
        Redis::del($attemptKey);

        return $user;
    }

    /**
     * Resend WEB registration OTP with a cooldown.
     */
    public function resendWebCode(string $email): bool
    {
        $email = strtolower(trim($email));

        $key = "register:web:{$email}";
        $resendKey = "register:web:resend:{$email}";

        if (Redis::exists($resendKey)) {
            return false;
        }

        $stored = Redis::get($key);

        if (! $stored) {
            return false;
        }

        $data = json_decode($stored, true);

        if (! $data) {
            return false;
        }

        $otp = $this->generateCode();

        $data['otp_hash'] = Hash::make($otp);

        Redis::setex(
            $key,
            $this->verificationExpiry * 60,
            json_encode($data)
        );

        Redis::setex($resendKey, $this->resendCooldown, 1);

        event(new \App\Events\UserRegistered($email, $otp));

        return true;
    }


    /** Store OTP in Redis */
    public function storeTempUser(array $data, ?UploadedFile $image = null): void
    {
        if ($image) {
            $data['image'] = $image;
        }

        $data['code'] = $this->generateCode();
        Redis::setex("register:{$data['email']}", $this->verificationExpiry * 60, json_encode($data));

        event(new UserRegistered($data['email'], $data['code']));
    }

    /** Resend OTP */
    public function resendCode(string $email): ?string
    {
        $resendKey = "resend_limit:{$email}";
        if (Redis::exists($resendKey)) {
            return null;
        }

        $stored = Redis::get("register:{$email}");
        if (! $stored) {
            return null;
        }

        $data = json_decode($stored, true);
        $data['code'] = $this->generateCode();

        Redis::setex("register:{$email}", $this->verificationExpiry * 60, json_encode($data));
        Redis::setex($resendKey, $this->resendCooldown, true);

        event(new UserRegistered($data['email'], $data['code']));

        return $data['code'];
    }

    /** Verify OTP */
    /** Verify OTP */
    public function verifyCode(string $email, string $code): ?array
    {
        $stored = Redis::get("register:{$email}");
        if (! $stored) {
            return null;
        }

        $data = json_decode($stored, true);
        if (! $data || ! isset($data['code'])) {
            return null;
        }

        if ((string) $data['code'] !== (string) $code) {
            return null;
        }

        // Delete temp OTP
        Redis::del("register:{$email}");

        // 1️⃣ Create user
        $user = $this->userRepository->create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'] ?? null,
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => $data['password'],
        ]);

        // Handle image
        if (! empty($data['image'])) {
            $this->handleMedia($user, $data['image']);
        }

        return [
            'user' => $user,
            'role' => $user->getRoleNames()->first(),
            'message' => 'Registration successful',
        ];
    }

    /** Generate 6-digit OTP */
    protected function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /** Send OTP email */
    // protected function sendVerificationEmail(string $email, string $code): void
    // {
    //     Mail::to($email)->send(new \App\Mail\VerificationCodeMail($code));
    // }
}
