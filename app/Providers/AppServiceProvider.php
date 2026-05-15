<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage; 
use Illuminate\Support\Facades\URL; 

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyEmail::createUrlUsing(function ($notifiable) {
            $verifyUrl = URL::temporarySignedRoute(
                'verification.verify',
                now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );
            return str_replace('http://localhost:8000/api', 'http://localhost:4200', $verifyUrl);
        });


        VerifyEmail::toMailUsing(function ($notifiable, $url) {
            return (new MailMessage)
                ->subject('⛓️ Aktiváld a RaveHouse fiókodat! ⛓️')
                ->greeting('Szia ' . $notifiable->name . '!')
                ->line('Köszönjük, hogy regisztráltál a RaveHouse-ra. Már csak egyetlen lépés választ el attól, hogy beléphess a klubba.')
                ->action('Fiók aktiválása', $url) // Ez lesz a kattintható nagy gomb
                ->line('A biztonsági link 60 percig érvényes.')
                ->line('Ha nem te indítottad a regisztrációt, nyugodtan hagyd figyelmen kívül ezt a levelet.')
                ->salutation('Üdvözlettel, a RaveHouse csapata 🖤');
        });
    }
}