<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreContactRequest;
use App\Mail\ContactMailToAdmin;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request): JsonResponse
    {
        $contact = Contact::query()->create($request->safe()->only([
            'name', 'phone', 'email', 'subject', 'message',
        ]));

        $adminEmail = 'cogentdevs@gmail.com';

        if (filter_var($adminEmail, FILTER_VALIDATE_EMAIL) !== false) {
            try {
                Mail::to($adminEmail)->send(new ContactMailToAdmin($contact));
            } catch (\Throwable $exception) {
                report($exception);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Your message has been received successfully.',
            'data' => ['contact_id' => $contact->getKey()],
        ], 201);
    }
}
