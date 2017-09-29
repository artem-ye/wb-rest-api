<?php

include_once('simple_html_dom.php');

class WB_ReviewParser
{

    public function getItemReviews($articul) {

        $url  = $this->BuildReviewURL($articul);
        $html = file_get_html($url);
        return $this->ParseReviews($html);

    }

    public function getItemRating($articul) {

        $reviews        = $this->getItemReviews($articul);
        $reviewsCount   = count($reviews);
        $rating         = 0;
        $lastReviewDate = '';

        if ($reviewsCount > 0) {

            foreach ($reviews as $review)
                $rating += (int)$review['RatingValue'];

            $lastReviewDate = $reviews[0]['TimeStamp'];
            $rating = ($reviewsCount==0 ? 0 : $rating/$reviewsCount);
        }

        return array (
            'ReviewsCount'   => $reviewsCount,
            'Rating'         => $rating,
            'LastReviewDate' => $lastReviewDate,
        );

    }

    private function buildReviewURL($articul) {

        return 'https://www.wildberries.ru/catalog/' . $articul . '/otzyvy?field=Date&order=Asc';

    }

    private function parseReviews($html) {

        $arrRetVal 		= array();
        $arrFieldList 	= array(
            'TimeStamp'		=> "div[itemprop=datePublished]",
            'RatingValue'	=> "div[class=comment-rating]",
            'Comment'		=> "p[itemprop=reviewBody]",
        );

        foreach ($html->find('div[class=comment]') as $commentBlock) {
            $arrFieldListValues = $arrFieldList;

            foreach ($arrFieldList as $key => $value) {
                $res = $commentBlock->find($value, 0)->innertext;
                $arrFieldListValues[$key] = $res;
            }

            $arrRetVal[] = $arrFieldListValues;
        }

        return $arrRetVal;

    }


}

?>