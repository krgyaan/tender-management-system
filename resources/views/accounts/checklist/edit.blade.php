@extends('layouts.app')
@section('page-title', 'Edit Account Checklist')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="#">
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="task_name" class="form-label">Task Name</label>
                                    <input type="text" class="form-control" name="task_name" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="frequency" class="form-label">Frequency</label>
                                    <select class="form-control" name="frequency" required>
                                        <option value="">Choose</option>
                                        <option value="Daily">Daily</option>
                                        <option value="Monthly">Monthly</option>
                                        <option value="Quarterly">Quarterly</option>
                                        <option value="Annual">Annual</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="responsibility" class="form-label">Responsibility</label>
                                    <input type="text" class="form-control" name="responsibility" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="accountability" class="form-label">Accountability</label>
                                    <input type="text" class="form-control" name="accountability" required>
                                </div>
                            </div>

                            <div class="text-end">
                                <button class="btn btn-primary" type="submit">Save Task</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
