<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class ManageAdministrator extends Command
{
    protected $signature = 'nihonreach:admin {--reset-password : Reset an existing administrator password}';

    protected $description = '交互创建管理员或重置管理员密码；仅供有容器终端权限的运维人员使用';

    public function handle(): int
    {
        $email = Str::lower(trim((string) $this->ask('管理员邮箱')));
        if (Validator::make(['email' => $email], ['email' => ['required', 'email:rfc', 'max:255']])->fails()) {
            $this->error('邮箱格式不正确。');

            return self::FAILURE;
        }

        $user = User::query()->where('email', $email)->first();
        $reset = (bool) $this->option('reset-password');
        if (($reset && ! $user?->is_admin) || (! $reset && $user !== null)) {
            $this->error('创建时邮箱必须未被使用；重置时必须是已有管理员。');

            return self::FAILURE;
        }

        $name = $reset ? $user->name : (string) $this->ask('管理员显示名');
        $password = $this->secret('新密码（至少 12 位，含大小写字母、数字和符号）', false);
        $confirmation = $this->secret('再次输入新密码', false);
        $validator = Validator::make([
            'name' => $name,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(12)->mixedCase()->numbers()->symbols()],
        ]);
        if ($validator->fails()) {
            $this->error('显示名或密码不符合要求，或两次密码不一致；未做修改。');

            return self::FAILURE;
        }

        $user ??= new User;
        DB::transaction(function () use ($user, $email, $name, $password): void {
            // Verification is an explicit operator action, not public self-registration.
            $user->forceFill([
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'is_admin' => true,
                'email_verified_at' => now(),
                'remember_token' => Str::random(60),
            ])->save();
        });

        if ($reset && config('session.driver') === 'database') {
            DB::connection(config('session.connection'))
                ->table(config('session.table', 'sessions'))
                ->where('user_id', $user->id)
                ->delete();
        }

        $this->info('管理员已保存。邮箱由运维确认，不发送邮件；已有双因素认证和 Passkey 保持不变。');

        return self::SUCCESS;
    }
}
