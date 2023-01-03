<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Report;
use App\Models\Block;

class ModController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response JSON object that represents eliminated report
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
     * @return \Illuminate\Http\Response Returns a new report object.
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
