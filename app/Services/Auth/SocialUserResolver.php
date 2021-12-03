<?php
namespace App\Services\Auth;

use App\Models\User;
use Exception;
use Coderello\SocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Facades\Socialite;

class SocialUserResolver implements SocialUserResolverInterface
{
    /**
     * Resolve user by provider credentials.
     *
     * @param string $provider
     * @param string $accessToken
     *
     * @return Authenticatable|null
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
        
        return User::where('email', $providerUser->getEmail())
            ->where('provider_id', $providerUser->getId())
            ->where('provider', $provider)
            ->firstOrFail();
    }
}