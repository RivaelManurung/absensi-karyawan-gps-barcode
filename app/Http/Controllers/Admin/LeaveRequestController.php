<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveRequestController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin/sakit yang perlu di-approve
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['user.division', 'user.jobTitle', 'status'])
            ->whereHas('status', function($q) {
                $q->whereIn('name', ['pending', 'excused', 'sick']);
            })
            ->whereNotNull('note');

        // Filter berdasarkan status approval (pending/approved/rejected)
        if ($request->has('filter') && $request->filter !== '') {
            if ($request->filter === 'pending') {
                $query->whereHas('status', function($q) {
                    $q->where('name', 'pending');
                });
            } elseif ($request->filter === 'approved') {
                $query->whereHas('status', function($q) {
                    $q->whereIn('name', ['excused', 'sick']);
                })->whereNotNull('approved_at');
            } elseif ($request->filter === 'rejected') {
                $query->whereNotNull('rejected_at');
            }
        }

        // Filter berdasarkan tanggal
        if ($request->has('date') && $request->date !== '') {
            $query->whereDate('date', $request->date);
        }

        $leaveRequests = $query->latest()->paginate(15);

        return view('admin.leave-requests.index', compact('leaveRequests'));
    }

    /**
     * Approve pengajuan izin/sakit
     */
    public function approve(Request $request, $id)
    {
        $attendance = Attendance::with('status')->findOrFail($id);
        
        // Pastikan ini adalah pengajuan pending
        if ($attendance->status->name !== 'pending') {
            return back()->with('error', 'Data yang dipilih bukan pengajuan yang menunggu persetujuan.');
        }

        // Tentukan status baru berdasarkan jenis pengajuan dalam note atau input tambahan
        // Untuk sementara kita gunakan 'excused' sebagai default, nanti bisa ditambahkan field jenis
        $approvedStatus = Status::where('name', 'excused')->first();

        $attendance->update([
            'status_id' => $approvedStatus->id,
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'rejected_at' => null,
            'rejection_reason' => null
        ]);

        return back()->with('success', 'Pengajuan telah disetujui.');
    }

    /**
     * Reject pengajuan izin/sakit
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:255'
        ]);

        $attendance = Attendance::with('status')->findOrFail($id);
        
        // Pastikan ini adalah pengajuan pending
        if ($attendance->status->name !== 'pending') {
            return back()->with('error', 'Data yang dipilih bukan pengajuan yang menunggu persetujuan.');
        }

        $rejectedStatus = Status::where('name', 'rejected')->first();

        $attendance->update([
            'status_id' => $rejectedStatus->id,
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => null
        ]);

        return back()->with('success', 'Pengajuan telah ditolak.');
    }

    /**
     * Menampilkan detail pengajuan
     */
    public function show($id)
    {
        $attendance = Attendance::with(['user.division', 'user.jobTitle', 'approvedBy', 'rejectedBy'])
            ->findOrFail($id);

        return view('admin.leave-requests.show', compact('attendance'));
    }
}
