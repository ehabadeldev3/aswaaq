<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\JobRequest;
use App\Models\Job;
use App\Traits\Message;
use Illuminate\Http\Request;

class JobController extends Controller
{
    use Message;

    public function __construct()
    {
        $this->middleware('permission:job read', ['only' => ['index','activeJob']]);
        $this->middleware('permission:job create', ['only' => ['store','create']]);
        $this->middleware('permission:job edit', ['only' => ['update', 'edit','activationJob']]);
        $this->middleware('permission:job delete', ['only' => ['destroy']]);
    }

    /**
     * get active Job
     */
    public function activeJob()
    {
        $jobs = Job::where('active', 1)->get();
        return $this->sendResponse(['jobs' => $jobs], 'Data exited successfully');
    }


    /**
     * activation Job
     */
    public function activationJob(Job $job)
    {
        $job->update([
            "active" => $job->active == 1 ?  0 : 1
        ]);
        return $this->sendResponse([], 'Data exited successfully');
    }


    public function index(Request $request)
    {
        $jobs = Job::when($request->search, function ($q) use ($request) {
            return $q->where('name_ar', 'like', '%' . $request->search . '%')->orWhere('name_en', 'like', '%' . $request->search . '%');
        })->latest()->paginate(10);

        return $this->sendResponse(['jobs' => $jobs], 'Data exited successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(JobRequest $request)
    {
        Job::create($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }

    public function edit(Job $job)
    {
        return $this->sendResponse(['job' => $job], 'Data exited successfully');
    }



    public function update(JobRequest $request, Job $job)
    {
        $job->update($request->validated());
        return $this->sendResponse([], 'Data exited successfully');
    }


    public function destroy(Job $job)
    {
        if (count($job->employees) == 0)
        {
            $job->delete();
            return $this->sendResponse([],'Deleted successfully');
        }
        return $this->sendError('Cant delete');

    }
}
