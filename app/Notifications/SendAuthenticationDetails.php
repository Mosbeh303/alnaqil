<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendAuthenticationDetails extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public $data ;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->greeting(trans('welcomeFirstTime'))
                    ->subject(trans('Login_data_to_your_account_on_the_local_carrier_platform'))
                    ->line(trans('Below_you_will_find_the_login_information_for_your_account_on_the_local_carrier_platform'))
                    ->line(trans('User_name') . ':' . $this->data['username'])
                    ->line(trans('mot_de_passe') . ':' . $this->data['password'])
                    ->action(trans('Log_in_now'), url('/ar/login'))
                    ->line(trans('Thank_you_for_installing_our_application_on_the_basket'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
