@extends('layouts.app')
@section('page-title', 'Account Checklists Report')
@section('content')
    <section>
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    @include('partials.messages')
                    <div class="card-body">
                        <form action="{{ route('checklists.calendar') }}" method="get" class="row" id="calendarForm">
                            @csrf
                            <div class="col-md-3">
                                <label class="form-label" for="user">Team Member</label>
                                <select name="user" id="user" class="form-control" required>
                                    @if (in_array(Auth::user()->role, ['admin', 'coordinator']))
                                        <option value="">Select Member</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}" {{ isset($selectedUser) && $selectedUser == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="{{ Auth::user()->id }}" selected>{{ Auth::user()->name }}</option>
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label" for="month">Month</label>
                                <input type="month" name="month" id="month" class="form-control"
                                    value="{{ $selectedMonth ?? date('Y-m') }}">
                                <small class="text-muted">
                                    Press space bar to select month.
                                </small>
                            </div>
                            {{-- <div class="col-md-3">
                                <button type="submit" class="btn btn-primary mt-4">Search</button>
                            </div> --}}
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Display -->
        <div class="card mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div id="calendar-container">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h4 id="calendar-month-year"></h4>
                            </div>
                            <div class="calendar-grid">
                                <!-- Weekday headers -->
                                <div class="calendar-weekdays">
                                    @foreach (['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <div class="calendar-weekday d-md-inline-block d-none" data-bs-toggle="tooltip"
                                            title="{{ $day }}" data-bs-placement="top">
                                            {{ substr($day, 0, 3) }}
                                        </div>
                                        <div class="calendar-weekday d-inline-block d-md-none">
                                            {{ $day[0] }}
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Days grid will be populated by JavaScript -->
                                <div class="calendar-days" id="calendar-days">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="each-day-report col-md-8">
                        <h5 class="mb-4">Report for <span id="report-date"></span></h5>
                        <ul class="nav nav-tabs mb-3" id="taskTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="resp-tab" data-bs-toggle="tab" data-bs-target="#resp"
                                    type="button" role="tab" aria-controls="resp" aria-selected="true">Responsibility
                                    Tasks</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="acct-tab" data-bs-toggle="tab" data-bs-target="#acct"
                                    type="button" role="tab" aria-controls="acct" aria-selected="false">Accountability
                                    Tasks</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="taskTabsContent">
                            <div class="tab-pane fade show active" id="resp" role="tabpanel" aria-labelledby="resp-tab">
                                <div class="table-responsive">
                                    <table class="newTable w-100 table-hover">
                                        <thead>
                                            <tr>
                                                <th>Task Name</th>
                                                <th>Completion Remark</th>
                                                <th>Completed At</th>
                                            </tr>
                                        </thead>
                                        <tbody id="report-list-resp">
                                            <!-- Responsibility tasks will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="acct" role="tabpanel" aria-labelledby="acct-tab">
                                <div class="table-responsive">
                                    <table class="newTable w-100 table-hover">
                                        <thead>
                                            <tr>
                                                <th>Task Name</th>
                                                <th>Completion Remark</th>
                                                <th>Completed At</th>
                                            </tr>
                                        </thead>
                                        <tbody id="report-list-acct">
                                            <!-- Accountability tasks will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, minmax(20px, 1fr));
            gap: 4px;
        }

        .calendar-weekdays {
            display: contents;
        }

        .calendar-weekday {
            text-align: center;
            font-weight: bold;
            padding: 8px;
            font-size: 14px;
        }

        .calendar-days {
            display: contents;
        }

        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid;
            position: relative;
        }

        .calendar-day:hover {
            transform: scale(1.01);
            background-color: #f0f0f0;
        }

        .calendar-day.empty {
            visibility: hidden;
        }

        .calendar-day.has-tasks {
            font-weight: bold;
            flex-direction: column;
        }

        .calendar-day.has-tasks .percentage {
            font-size: 12px;
            position: absolute;
            top: 4px;
            right: 4px;
        }

        .newTable {
            width: 100%;
            border-collapse: collapse;
        }

        .newTable th {
            font-weight: bold;
            text-transform: uppercase
        }

        .newTable th,
        .newTable td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const monthInput = document.getElementById('month');
            const calendarDays = document.getElementById('calendar-days');
            const calendarMonthYear = document.getElementById('calendar-month-year');

            let currentDate = new Date();
            let tasksData = {};

            function initCalendar() {
                const monthValue = monthInput.value;
                if (monthValue) {
                    const [year, month] = monthValue.split('-');
                    currentDate = new Date(year, month - 1, 1);
                }
                renderCalendar();
                fetchTasksData();
            }

            function renderCalendar() {
                calendarDays.innerHTML = '';

                const year = currentDate.getFullYear();
                const month = currentDate.getMonth();

                // Set month/year header
                calendarMonthYear.textContent = new Intl.DateTimeFormat('en-US', {
                    year: 'numeric',
                    month: 'long'
                }).format(currentDate);

                // Set month input value
                monthInput.value = `${year}-${String(month + 1).padStart(2, '0')}`;

                // Get first day of month and how many days in month
                const firstDay = new Date(year, month, 1).getDay();
                const daysInMonth = new Date(year, month + 1, 0).getDate();

                // Add empty cells for days before first day of month
                for (let i = 0; i < firstDay; i++) {
                    const emptyDay = document.createElement('div');
                    emptyDay.className = 'calendar-day empty';
                    calendarDays.appendChild(emptyDay);
                }

                // Add cells for each day of month
                for (let day = 1; day <= daysInMonth; day++) {
                    const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const dayElement = document.createElement('div');
                    dayElement.className = 'calendar-day';
                    dayElement.textContent = day;
                    dayElement.dataset.date = dateStr;

                    // Check if day has tasks (will be updated after data fetch)
                    if (tasksData[dateStr] && tasksData[dateStr].length > 0) {
                        dayElement.classList.add('has-tasks');
                        dayElement.style.backgroundColor = getColorForTaskCount(tasksData[dateStr].length);
                    }

                    dayElement.addEventListener('click', () => showDayTasks(dateStr));
                    calendarDays.appendChild(dayElement);
                }
            }

            function getColorForTaskCount(percent) {
                if (percent === 0) return '#ebedf0';
                if (percent <= 25) return '#f66';
                if (percent <= 50) return '#ffcc00';
                if (percent <= 75) return '#40c463';
                return '#216e39';
            }

            function fetchTasksData() {
                const userId = document.getElementById('user').value;
                const month = monthInput.value;

                if (!userId) return;

                // Add CSRF token to headers
                const headers = {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                };

                fetch(`/accounts/checklists/tasks?user=${userId}&month=${month}`, {
                    headers: headers
                })
                    .then(response => {
                        console.log('response', response);

                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        tasksData = data;
                        updateCalendarDays();
                    })
                    .catch(error => {
                        console.error('Error fetching tasks:', error);
                        // Show error to user
                        alert('Failed to load tasks. Please try again.');
                    });
            }

            function updateCalendarDays() {
                document.querySelectorAll('.calendar-day:not(.empty)').forEach(day => {
                    const dateStr = day.dataset.date;
                    const taskInfo = tasksData[dateStr];

                    day.innerHTML = ''; // Clear previous

                    const dayNumber = document.createElement('div');
                    dayNumber.textContent = day.dataset.date.split('-')[2]; // Day
                    dayNumber.style.fontWeight = 'bold';

                    day.appendChild(dayNumber);

                    if (taskInfo && taskInfo.tasks.length > 0) {
                        const percent = taskInfo.percentage;

                        const percentLabel = document.createElement('small');
                        percentLabel.className = 'percentage';
                        percentLabel.textContent = `${percent}%`;
                        day.appendChild(percentLabel);

                        day.classList.add('has-tasks');
                        // Bucket fill effect: green for percent, gray for rest
                        day.style.background = `linear-gradient(to top, #40c463 ${percent}%, #ebedf0 ${percent}%)`;
                    } else {
                        day.classList.remove('has-tasks');
                        day.style.background = '';
                    }
                });
            }

            function showDayTasks(dateStr) {
                const date = new Date(dateStr);
                document.getElementById('report-date').textContent = date.toDateString();

                const tasksListResp = document.getElementById('report-list-resp');
                const tasksListAcct = document.getElementById('report-list-acct');
                tasksListResp.innerHTML = '';
                tasksListAcct.innerHTML = '';

                const taskInfo = tasksData[dateStr];
                if (taskInfo && taskInfo.tasks.length > 0) {
                    const userId = document.getElementById('user').value;
                    // Split tasks
                    const respTasks = taskInfo.tasks.filter(task => task.responsible_user_id == userId);
                    const acctTasks = (taskInfo.accountability_tasks || []);

                    // Responsibility tasks
                    if (respTasks.length > 0) {
                        respTasks.forEach(task => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                        <td>${task.task_name}</td>
                                        <td>${task.remark || '-'}</td>
                                        <td>${task.completed_at || 'Not completed'}</td>
                                    `;
                            tasksListResp.appendChild(row);
                        });
                    } else {
                        tasksListResp.innerHTML = '<tr><td colspan="3" class="text-center">No responsibility tasks for this day</td></tr>';
                    }

                    // Accountability tasks
                    if (acctTasks.length > 0) {
                        acctTasks.forEach(task => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                        <td>${task.task_name}</td>
                                        <td>${task.remark || '-'}</td>
                                        <td>${task.completed_at || 'Not completed'}</td>
                                    `;
                            tasksListAcct.appendChild(row);
                        });
                    } else {
                        tasksListAcct.innerHTML = '<tr><td colspan="4" class="text-center">No accountability tasks for this day</td></tr>';
                    }
                } else {
                    tasksListResp.innerHTML = '<tr><td colspan="3" class="text-center">No tasks for this day</td></tr>';
                    tasksListAcct.innerHTML = '<tr><td colspan="4" class="text-center">No tasks for this day</td></tr>';
                }
            }


            // Event listeners
            monthInput.addEventListener('change', initCalendar);
            document.getElementById('user').addEventListener('change', fetchTasksData);
            document.getElementById('calendarForm').addEventListener('submit', function (e) {
                e.preventDefault();
                initCalendar();
            });

            // Initialize on page load
            initCalendar();
        });
    </script>
@endpush
