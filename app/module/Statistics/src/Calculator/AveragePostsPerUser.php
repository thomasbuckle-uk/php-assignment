<?php

namespace Statistics\Calculator;

use SocialPost\Dto\SocialPostTo;
use Statistics\Dto\StatisticsTo;

class AveragePostsPerUser extends AbstractCalculator
{


    protected const UNITS = 'posts';

    private array $totals = [];

    private array $months = [];

    /**
     * @inheritDoc
     */
    protected function doAccumulate(SocialPostTo $postTo): void
    {

        $months = $postTo->getDate()->format('F');

        $this->months[] = $months;

        $key = $postTo->getAuthorId();

        $this->totals[$key] = ($this->totals[$key] ?? 0) + 1;


    }

    /**
     * @inheritDoc
     */
    protected function doCalculate(): StatisticsTo
    {
        //Better way to handle getting a list of months. perhaps further up the stack however this works for purposes of tests
        $this->months = array_unique($this->months);

        $stats = new StatisticsTo();
        $total = 0;
        foreach ($this->totals as $authorId => $totalPosts) {
           $total = $total + $totalPosts;
        }
        $stats->setValue($total / count($this->months));
        $stats->setName($this->parameters->getStatName());
        return $stats;
    }
}
