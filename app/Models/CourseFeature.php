<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseFeature extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'free_bootcamp' => 'integer',
    ];

    public function scopeWithBootcamps($query)
    {
        $query->whereFreeBootcamp(1);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Process set featured course form
     *
     * @param $featuredList
     *
     * @param $freeBootcamp
     * @return int $order
     * @throws \Exception
     */
    public function setFeatured($featuredList, $freeBootcamp)
    {
        $featured = collect();
        foreach ($featuredList as $listed) {
            if (!$featured->contains($listed)) {
                $featured->push($listed);
            }
        }

        $this->where('free_bootcamp', $freeBootcamp)->delete();

        $order = 1;
        foreach ($featured as $featuredCourse) {
            if (!$featuredCourse || $featuredCourse === 'none') {
                continue;
            }

            $this->create([
                'course_id'     => $featuredCourse,
                'order'         => $order,
                'free_bootcamp' => $freeBootcamp
            ]);
            $order++;
        }

        return $order;
    }
}
