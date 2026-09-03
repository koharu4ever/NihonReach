<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdministratorCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_create_a_verified_administrator(): void
    {
        $this->artisan('nihonreach:admin')
            ->expectsQuestion('管理员邮箱', 'operator@example.test')
            ->expectsQuestion('管理员显示名', 'Demo Operator')
            ->expectsQuestion('新密码（至少 12 位，含大小写字母、数字和符号）', 'Synthetic-Pass123!')
            ->expectsQuestion('再次输入新密码', 'Synthetic-Pass123!')
            ->assertSuccessful();

        $user = User::query()->sole();
        $this->assertTrue($user->is_admin);
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('Synthetic-Pass123!', $user->password));
    }

    public function test_command_will_not_promote_or_overwrite_an_existing_user(): void
    {
        $user = User::factory()->create();
        foreach (['nihonreach:admin', 'nihonreach:admin --reset-password'] as $command) {
            $this->artisan($command)->expectsQuestion('管理员邮箱', $user->email)->assertFailed();
        }
        $this->assertFalse($user->fresh()->is_admin);
    }

    public function test_operator_can_reset_a_password_and_revoke_database_sessions(): void
    {
        config(['session.driver' => 'database']);
        $admin = User::factory()->admin()->create();
        DB::table('sessions')->insert([
            'id' => 'synthetic-session', 'user_id' => $admin->id,
            'payload' => '', 'last_activity' => time(),
        ]);
        $this->artisan('nihonreach:admin --reset-password')
            ->expectsQuestion('管理员邮箱', $admin->email)
            ->expectsQuestion('新密码（至少 12 位，含大小写字母、数字和符号）', 'Synthetic-Reset123!')
            ->expectsQuestion('再次输入新密码', 'Synthetic-Reset123!')
            ->assertSuccessful();

        $this->assertTrue(Hash::check('Synthetic-Reset123!', $admin->fresh()->password));
        $this->assertDatabaseMissing('sessions', ['id' => 'synthetic-session']);
    }

    public function test_invalid_password_does_not_create_an_account(): void
    {
        $this->artisan('nihonreach:admin')
            ->expectsQuestion('管理员邮箱', 'operator@example.test')
            ->expectsQuestion('管理员显示名', 'Demo Operator')
            ->expectsQuestion('新密码（至少 12 位，含大小写字母、数字和符号）', 'short')
            ->expectsQuestion('再次输入新密码', 'different')
            ->assertFailed();
        $this->assertDatabaseCount('users', 0);
    }
}
