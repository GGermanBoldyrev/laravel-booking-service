<?php

namespace App\Rules;

use App\Models\BookingSlot;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoOverlap implements ValidationRule
{
    private ?int $ignoreSlotId;

    public function __construct(int $ignoreSlotId = null)
    {
        $this->ignoreSlotId = $ignoreSlotId;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $request = request();

        $startTime = Carbon::parse($value);

        if (str_contains($attribute, 'slots.')) {
            $index = explode('.', $attribute)[1];
            $endTime = Carbon::parse($request->input("slots.{$index}.end_time"));
        } else {
            $endTime = Carbon::parse($request->input('end_time'));
        }

        if ($endTime->lte($startTime)) {
            $fail('End time must be after start time.');
            return;
        }

        $query = BookingSlot::query()
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($this->ignoreSlotId) {
            $query->where('id', '!=', $this->ignoreSlotId);
        }

        if ($query->exists()) {
            $fail('The selected time slot overlaps with an existing booking.');
        }
    }
}
