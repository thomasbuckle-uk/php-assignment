<?php

namespace Tests\unit;

use DateTime;
use SocialPost\Dto\SocialPostTo;
use Statistics\Builder\ParamsBuilder;
use Statistics\Calculator\TotalPostsPerWeek;
use PHPUnit\Framework\TestCase;
use Statistics\Dto\ParamsTo;
use Statistics\Dto\StatisticsTo;

class TotalPostsPerWeekTest extends TestCase
{

    public function testDoAccumulate()
    {
        $calculator = new TotalPostsPerWeek();
        $postTo = new SocialPostTo();
        $postTo->setDate(new DateTime('2023-04-25'));
        $calculator->doAccumulate($postTo);
        $totals = $calculator->getTotals();

        $this->assertArrayHasKey('Week 17, 2023', $totals);
        $this->assertEquals(1, $totals['Week 17, 2023']);

    }

    public function testDoCalculate(): void
    {


        $startDate = new DateTime('2023-04-23');
        $endDate = new DateTime('2023-04-24');

        // Arrange
        $calculator = new TotalPostsPerWeek();


        $statsParam = new ParamsTo();

        $statsParam->setStatName('Total Posts Per Week');
        $statsParam->setStartDate($startDate);
        $statsParam->setEndDate($endDate);


        $calculator->setParameters($statsParam );

        $postTo1 = new SocialPostTo();
        $postTo1->setDate($startDate);
        $calculator->doAccumulate($postTo1);

        $postTo2 = new SocialPostTo();
        $postTo2->setDate($endDate);
        $calculator->doAccumulate($postTo2);

        // Act
        $stats = $calculator->calculate();

        // Assert
        $this->assertInstanceOf(StatisticsTo::class, $stats);

        $children = $stats->getChildren();
        $this->assertCount(2, $children);

        $this->assertEquals('posts', $stats->getUnits());

        $child1 = $children[0];
        $this->assertInstanceOf(StatisticsTo::class, $child1);
        $this->assertEquals('Total Posts Per Week', $child1->getName());
        $this->assertEquals('Week 16, 2023', $child1->getSplitPeriod());
        $this->assertEquals(1, $child1->getValue());
        $this->assertEquals('posts', $child1->getUnits());

        $child2 = $children[1];
        $this->assertInstanceOf(StatisticsTo::class, $child2);
        $this->assertEquals('Total Posts Per Week', $child2->getName());
        $this->assertEquals('Week 17, 2023', $child2->getSplitPeriod());
        $this->assertEquals(1, $child2->getValue());
        $this->assertEquals('posts', $child2->getUnits());
    }
}

