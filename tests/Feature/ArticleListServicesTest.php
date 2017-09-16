<?php


namespace Dymantic\Articles\Test\Feature;


use Carbon\Carbon;
use Dymantic\Articles\Test\MakesModels;
use Dymantic\Articles\Test\TestCase;

class ArticleListServicesTest extends TestCase
{
    use MakesModels;

    /**
     *@test
     */
    public function a_paginated_list_of_articles_can_be_fetched()
    {
        $this->disableExceptionHandling();
        foreach(range(1,44) as $index) {
            $this->createArticle(['created_at' => Carbon::parse("-{$index} days")]);
        }

        $response = $this->asLoggedInUser()->json('GET', "/admin/services/articles");
        $response->assertStatus(200);

        $fetched_articles = $response->decodeResponseJson();

        $this->assertCount(15, $fetched_articles['articles']);
        $this->assertCount(3, $fetched_articles['page_links']);
        $this->assertEquals(1, $fetched_articles['current_page']);

    }

    /**
     *@test
     */
    public function a_specific_page_of_articles_can_be_returned()
    {
        $this->disableExceptionHandling();
        foreach(range(1,44) as $index) {
            $this->createArticle(['created_at' => Carbon::parse("-{$index} days")]);
        }

        $responseA = $this->asLoggedInUser()->json('GET', "/admin/services/articles?page=1");
        $responseA->assertStatus(200);

        $responseB = $this->asLoggedInUser()->json('GET', "/admin/services/articles?page=2");
        $responseB->assertStatus(200);

        $responseC = $this->asLoggedInUser()->json('GET', "/admin/services/articles?page=3");
        $responseC->assertStatus(200);

        $page1 = $responseA->decodeResponseJson();
        $page1_ids = collect($page1['articles'])->pluck('id');
        $page2 = $responseB->decodeResponseJson();
        $page2_ids = collect($page2['articles'])->pluck('id');
        $page3 = $responseC->decodeResponseJson();
        $page3_ids = collect($page3['articles'])->pluck('id');

        $this->assertCount(0, $page1_ids->intersect($page2_ids));
        $this->assertCount(0, $page1_ids->intersect($page3_ids));
        $this->assertCount(0, $page2_ids->intersect($page3_ids));
    }
}