<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormSubmitted; // Notre Mailable
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log; // Pour logger les erreurs
// $siteSettings sera injecté globalement, pas besoin de l'importer ici sauf si vous ne l'avez pas fait globalement

class ContactController extends Controller
{
    /**
     * Show the contact form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        // $siteSettings est disponible globalement via ShareSiteSettings middleware
        return view('public.contact'); 
    }

    /**
     * Handle the contact form submission.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submitForm(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ];

        // Ajouter reCAPTCHA seulement si les clés sont configurées
        if (config('services.recaptcha.key') && config('services.recaptcha.secret')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $messages = [
            'name.required' => __('Votre nom complet est requis.'),
            'email.required' => __('Votre adresse email est requise.'),
            'email.email' => __('Veuillez fournir une adresse email valide.'),
            'subject.required' => __('Le sujet de votre message est requis.'),
            'message.required' => __('Veuillez écrire votre message.'),
            'g-recaptcha-response.required' => __('Veuillez compléter la vérification reCAPTCHA.'),
            'g-recaptcha-response.captcha' => __('La vérification reCAPTCHA a échoué. Veuillez réessayer.'),
        ];
        
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->route('public.contact.form')
                        ->withErrors($validator)
                        ->withInput();
        }
        
        $siteSettings = app('siteSettings'); // Accéder à la variable globale
        $recipientEmail = $siteSettings->contact_email ?: config('mail.from.address');

        if (empty($recipientEmail)) {
            Log::error('Contact form error: Recipient email is not configured.');
            return redirect()->route('public.contact.form')
                             ->with('error', __('Le service de contact est temporairement indisponible. Veuillez réessayer plus tard.'))
                             ->withInput();
        }
        
        $data = $request->only('name', 'email', 'subject', 'message');

        try {
            Mail::to($recipientEmail)->send(new ContactFormSubmitted($data));

            return redirect()->route('public.contact.form')
                             ->with('success', __('Votre message a été envoyé avec succès ! Nous vous répondrons dès que possible.'));

        } catch (\Exception $e) {
            Log::error("Contact form submission error: " . $e->getMessage() . " Data: " . json_encode($data));
            return redirect()->route('public.contact.form')
                             ->with('error', __('Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer ou nous contacter directement.'))
                             ->withInput();
        }
    }
}