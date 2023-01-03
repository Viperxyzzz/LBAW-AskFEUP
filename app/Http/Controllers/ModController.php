<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Report;
use App\Models\Block;

class ModController extends Controller
{
    /**
     * Create a page to display relevant moderation info. 
     * This includes reports and blocked users.
     * 
     * @return mixed Return moderation dashboard page.
     */
    public function index()
    {
        if (!Auth::check()) return redirect('/login');
        if (!Auth::user()->is_mod()) return redirect('/');
        $reports = Report::all();
        $blocks = Block::all();
        return view('pages.dashboard', ['reports' => $reports, 'blocks' => $blocks]);
    }

    /**
     * Remove a report from storage.
     *
     * @param int $id Id of the report to be eliminated.
     * @return mixed JSON object that represents eliminated report
     */
    public function delete_report($report_id)
    {
        if (!Auth::check()) return redirect('/login');
        if (!Auth::user()->is_mod()) return redirect('/');
        $report = Report::find($report_id);
        $this->authorize('delete', $report);

        $report->delete();
        return $report;
    }

    /**
     * Create a new report.
     *
     * @return mixed Returns a new report object.
     */
    public function create_report(Request $request)
    {
        if (!Auth::check()) return redirect('/login');

        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $report = new Report;
        $report->reason = $request->input('reason');
        $report->date = date('Y-m-d H:i:s');
        $report->question_id = $request->question_id;
        $report->answer_id = $request->answer_id;
        $report->comment_id = $request->comment_id;

        $report->save();

        return $report;
    }

}
