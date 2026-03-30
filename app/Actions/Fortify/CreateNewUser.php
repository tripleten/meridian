<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Meridian\IdentityAccess\Domain\Events\UserRegistered;
use Meridian\IdentityAccess\Domain\User\UserRole;
use Meridian\Shared\Infrastructure\Outbox\OutboxWriter;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        return DB::transaction(function () use ($input): User {
            $user = User::create([
                'name'     => $input['name'],
                'email'    => $input['email'],
                'password' => $input['password'],
            ]);

            $user->assignRole(UserRole::Customer->value);

            app(OutboxWriter::class)->record(new UserRegistered(
                userId: $user->id,
                email:  $user->email,
            ));

            return $user;
        });
    }
}
