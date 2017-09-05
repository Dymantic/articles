<?php


namespace Dymantic\Articles;


use Dymantic\Articles\Events\ArticleFirstPublished;
use Illuminate\Support\Carbon;

trait Publishable
{

    public function scopePublished($query)
    {
        return $query->where('is_draft', false)->whereDate('published_on', '<=', Carbon::today());
    }

    public function scopeDrafts($query)
    {
        return $query->where('is_draft', true);
    }

    public function publish($date = null)
    {
        Carbon::parse($date)->isToday() ? $this->publishNow() : $this->publishOn($date);

        return $this->save();
    }

    protected function publishOn($date)
    {
        $this->published_on = Carbon::parse($date);
        $this->is_draft = false;
    }

    protected function publishNow()
    {
        if($this->hasNeverBeenPublished()) {
            event(new ArticleFirstPublished($this));
            $this->published_on = Carbon::now();
        }
        $this->is_draft = false;
    }

    protected function hasNeverBeenPublished()
    {
        return is_null($this->published_on);
    }

    public function retract()
    {
        $this->is_draft = true;

        return $this->save();
    }
}