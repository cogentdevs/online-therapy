<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(): View
    {
        return view('admin.contact.view-contact');
    }

    public function data(Request $request): JsonResponse
    {
        $query = Contact::query();
        $total = (clone $query)->count();
        $search = mb_substr(trim((string) $request->input('search.value', '')), 0, 100);

        if ($search !== '') {
            $query->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%')
                    ->orWhere('phone', 'like', '%'.$search.'%')
                    ->orWhere('subject', 'like', '%'.$search.'%');
            });
        }

        $filtered = (clone $query)->count();
        $columns = [1 => 'name', 2 => 'email', 3 => 'phone', 4 => 'subject', 5 => 'created_at', 6 => 'is_read'];
        $orderColumn = $columns[max(0, $request->integer('order.0.column'))] ?? 'created_at';
        $direction = $request->input('order.0.dir') === 'asc' ? 'asc' : 'desc';
        $length = min(max($request->integer('length', 10), 1), 100);
        $canDelete = $request->user()?->can('contacts.delete') ?? false;
        $contacts = $query->orderBy($orderColumn, $direction)
            ->orderByDesc('id')
            ->offset(max(0, $request->integer('start')))
            ->limit($length)
            ->get();

        return response()->json([
            'draw' => max(0, $request->integer('draw')),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $contacts->values()->map(fn (Contact $contact, int $index): array => [
                'serial' => max(0, $request->integer('start')) + $index + 1,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'subject' => $contact->subject,
                'received_at' => $contact->created_at?->format('d M Y, h:i A') ?? '',
                'is_read' => $contact->is_read,
                'show_url' => route('admin.contacts.show', $contact),
                'delete_url' => $canDelete ? route('admin.contacts.destroy', $contact) : null,
            ])->all(),
        ]);
    }

    public function show(Contact $contact): View
    {
        if (! $contact->is_read) {
            $contact->is_read = true;
            $contact->save();
        }

        return view('admin.contact.view-contact-detail', ['contact' => $contact]);
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $contact->delete();

        return redirect()->route('admin.contacts.index')->with('status', 'Contact message deleted successfully.');
    }
}
