<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display the inbox list (admin/super_admin CMS).
     */
    public function index(Request $request)
    {
        $query = Contact::latest();

        // Filter: read / unread
        if ($request->filled('status')) {
            if ($request->status === 'unread') {
                $query->where('is_read', false);
            } elseif ($request->status === 'read') {
                $query->where('is_read', true);
            }
        }

        // Search by name / email / subject
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($builder) use ($q) {
                $builder->where('name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%")
                    ->orWhere('subject', 'like', "%{$q}%");
            });
        }

        $contacts  = $query->paginate(15)->withQueryString();
        $unreadCount = Contact::where('is_read', false)->count();

        return view('contact.index', compact('contacts', 'unreadCount'));
    }

    /**
     * Show a single contact message and mark it as read.
     */
    public function show(Contact $contact)
    {
        if (!$contact->is_read) {
            $contact->update(['is_read' => true]);
        }

        return view('contact.show', compact('contact'));
    }

    /**
     * Mark a contact as read (AJAX / form action).
     */
    public function markRead(Contact $contact)
    {
        $contact->update(['is_read' => true]);

        return back()->with('success', 'Pesan ditandai sudah dibaca.');
    }

    /**
     * Mark a contact as unread.
     */
    public function markUnread(Contact $contact)
    {
        $contact->update(['is_read' => false]);

        return back()->with('success', 'Pesan ditandai belum dibaca.');
    }

    /**
     * Delete a contact message.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();

        return back()->with('success', 'Pesan berhasil dihapus.');
    }

    /**
     * Public: handle the guest contact form submission.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'nullable|string|max:30',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ], [
            'name.required'    => 'Nama wajib diisi.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'subject.required' => 'Subjek wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
        ]);

        Contact::create($validated);

        return back()->with('contact_success', 'Pesan Anda berhasil dikirim. Kami akan segera menghubungi Anda.');
    }
}
