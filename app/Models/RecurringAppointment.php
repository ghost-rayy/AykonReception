<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RecurringAppointment extends Model
{
    protected $fillable = [
        'visitor_name',
        'visitor_phone',
        'visitor_email',
        'user_id',
        'purpose',
        'notes',
        'appointment_time',
        'duration_minutes',
        'recurrence_type',
        'recurrence_interval',
        'recurrence_days',
        'start_date',
        'end_date',
        'occurrences',
        'status',
    ];

    protected $casts = [
        'appointment_time' => 'datetime:H:i',
        'recurrence_days' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointments::class, 'recurring_appointment_id');
    }

    public function generateAppointments($startDate = null, $endDate = null)
    {
        $startDate = $startDate ? Carbon::parse($startDate) : Carbon::now();
        $endDate = $endDate ? Carbon::parse($endDate) : ($this->end_date ? Carbon::parse($this->end_date) : Carbon::now()->addMonths(3));
        
        $appointments = [];
        $current = $startDate->copy();
        $count = 0;
        $maxOccurrences = $this->occurrences ?? 1000; // Safety limit

        while ($current <= $endDate && $count < $maxOccurrences) {
            $appointmentDate = $this->getNextAppointmentDate($current);
            
            if (!$appointmentDate || ($this->end_date && $appointmentDate->gt($this->end_date))) {
                break;
            }

            // Check if appointment already exists
            $exists = Appointments::where('recurring_appointment_id', $this->id)
                ->whereDate('appointment_time', $appointmentDate->format('Y-m-d'))
                ->exists();

            if (!$exists) {
                $appointmentTime = Carbon::parse($appointmentDate->format('Y-m-d') . ' ' . $this->appointment_time->format('H:i:s'));
                
                // Check for conflicts
                $conflict = Appointments::where('user_id', $this->user_id)
                    ->where('appointment_time', $appointmentTime)
                    ->where('status', '!=', 'canceled')
                    ->exists();

                if (!$conflict) {
                    $appointment = Appointments::create([
                        'visitor_name' => $this->visitor_name,
                        'visitor_phone' => $this->visitor_phone,
                        'visitor_email' => $this->visitor_email,
                        'user_id' => $this->user_id,
                        'purpose' => $this->purpose,
                        'notes' => $this->notes,
                        'appointment_time' => $appointmentTime,
                        'duration_minutes' => $this->duration_minutes,
                        'recurring_appointment_id' => $this->id,
                        'status' => 'pending',
                    ]);

                    $appointments[] = $appointment;
                    $count++;
                }
            }

            $current = $appointmentDate->copy()->addDay();
        }

        return $appointments;
    }

    private function getNextAppointmentDate($fromDate)
    {
        $from = Carbon::parse($fromDate);
        $start = Carbon::parse($this->start_date);
        
        if ($from->lt($start)) {
            $from = $start->copy();
        }

        switch ($this->recurrence_type) {
            case 'daily':
                return $from->copy();
                
            case 'weekly':
                if ($this->recurrence_days && is_array($this->recurrence_days) && !empty($this->recurrence_days)) {
                    $days = $this->recurrence_days;
                    sort($days);
                    
                    // Map day numbers to Carbon day constants (0=Sunday, 1=Monday, etc.)
                    $dayMap = [0 => Carbon::SUNDAY, 1 => Carbon::MONDAY, 2 => Carbon::TUESDAY, 
                               3 => Carbon::WEDNESDAY, 4 => Carbon::THURSDAY, 5 => Carbon::FRIDAY, 6 => Carbon::SATURDAY];
                    
                    foreach ($days as $dayNum) {
                        $carbonDay = $dayMap[$dayNum] ?? $dayNum;
                        $nextDate = $from->copy()->next($carbonDay);
                        if ($nextDate->gte($from)) {
                            return $nextDate;
                        }
                    }
                    // If no day found this week, get first day of next week
                    $firstDay = $dayMap[$days[0]] ?? $days[0];
                    return $from->copy()->next($firstDay)->addWeek();
                }
                return $from->copy()->addWeeks($this->recurrence_interval);
                
            case 'monthly':
                return $from->copy()->addMonths($this->recurrence_interval);
                
            case 'yearly':
                return $from->copy()->addYears($this->recurrence_interval);
                
            default:
                return null;
        }
    }

    public function pause()
    {
        $this->update(['status' => 'paused']);
    }

    public function resume()
    {
        $this->update(['status' => 'active']);
    }

    public function cancel()
    {
        $this->update(['status' => 'canceled']);
    }
}
