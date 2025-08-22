<div>
    <p style="font-weight: bold; text-align: center;">Account Checklist Report for {{ $data['date'] }}</p>
    @foreach ($data['tables'] as $table)
        <p style="font-weight: bold; text-align: center;">Responsible: {{ $table['responsible_user'] }}</p>
        <table style="width:100%; border-collapse: collapse; margin-bottom: 20px; border: 1px solid #ddd;">
            <thead>
                <tr>
                    <th style="font-weight: bold; padding: 8px;">Task Name</th>
                    <th style="font-weight: bold; padding: 8px;">Accountable</th>
                    <th style="font-weight: bold; padding: 8px;">Status</th>
                    <th style="font-weight: bold; padding: 8px;">Remark</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($table['tasks'] as $task)
                    <tr style="border: 1px solid #ddd;">
                        <td style="padding: 8px;">{{ $task['task_name'] }}</td>
                        <td style="padding: 8px;">{{ $task['accountable_user'] }}</td>
                        <td style="padding: 8px;">{{ $task['completed_at'] ? 'Completed' : 'Pending' }}</td>
                        <td style="padding: 8px;">{{ $task['remark'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <br>
    @endforeach
</div>
