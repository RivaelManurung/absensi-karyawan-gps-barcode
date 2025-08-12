<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LeaveRequestController extends Controller
{
    /**
     * Menampilkan daftar pengajuan izin/sakit yang perlu di-approve
     */
    public function index(Request $request)
    {
        $query = Attendance::with(['user.division', 'user.jobTitle', 'status', 'approvedBy', 'rejectedBy'])
            ->whereNotNull('note');

        // Get statistics before applying filters (for cards)
        $totalRequests = (clone $query)->count();
        $pendingCount = (clone $query)->whereNull('approved_at')->whereNull('rejected_at')->count();
        $approvedCount = (clone $query)->whereNotNull('approved_at')->whereNull('rejected_at')->count();
        $rejectedCount = (clone $query)->whereNotNull('rejected_at')->count();

        // Filter berdasarkan status approval (pending/approved/rejected)
        $filter = $request->get('filter');
        if (!empty($filter)) {
            if ($filter === 'pending') {
                $query->whereNull('approved_at')
                      ->whereNull('rejected_at');
            } elseif ($filter === 'approved') {
                $query->whereNotNull('approved_at')
                      ->whereNull('rejected_at');
            } elseif ($filter === 'rejected') {
                $query->whereNotNull('rejected_at');
            }
        }

        // Filter berdasarkan tanggal
        $date = $request->get('date');
        if (!empty($date)) {
            $query->whereDate('date', $date);
        }

        $leaveRequests = $query->latest()->paginate(15);

        // Prepare statistics
        $statistics = [
            'total' => $totalRequests,
            'pending' => $pendingCount,
            'approved' => $approvedCount,
            'rejected' => $rejectedCount,
        ];

        return view('admin.leave-requests.index', compact('leaveRequests', 'statistics'));
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

    /**
     * Download file attachment
     */
    public function downloadAttachment($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        if (!$attendance->attachment_path) {
            return back()->with('error', 'File tidak ditemukan.');
        }

        $filePath = storage_path('app/private/' . $attendance->attachment_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath, basename($attendance->attachment_path));
    }

    /**
     * View file attachment in browser
     */
    public function viewAttachment($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        if (!$attendance->attachment_path) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = storage_path('app/private/' . $attendance->attachment_path);
        
        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . basename($attendance->attachment_path) . '"'
        ]);
    }
}
