<?php

include_once('simple_html_dom.php');

class WB_ReviewParser
{

    public function getItemRatingByURL($url) {

        $retVal = array(
            'ReviewsCount'   => 0,
            'Rating'         => '-',
            'LastReviewDate' => '-',
        );

        // Parse reviews count and rating value from item main page
        // URL example:
        //  > https://www.wildberries.ru/catalog/4131529/detail.aspx?targetUrl=BP
        $itemMainPage = file_get_html($url);

        // Rating
        $res = $itemMainPage->find('span[itemprop=ratingValue]', 0);

        if ($res)
            $retVal['Rating'] = trim($res->plaintext);

        // Reviews count
        $res = $itemMainPage->find('a[id=a-Comments]', 0);

        if ($res) {
            preg_match('/[0-9]+/',  $res->plaintext, $matches);

            if ((int)$matches[0] > 0) {
                $retVal['ReviewsCount'] = $matches[0];

                # Searching for first review date
                # - item main URL:    https://www.wildberries.ru/catalog/4131529/detail.aspx?targetUrl=BP
                # - item reviews URL: https://www.wildberries.ru/catalog/4131529/otzyvy?field=Date&order=Desc
                # calculating reviews page url (replacing uri)
                $reviewsUrl = preg_replace('%(^[^0-9]+/[0-9]+/)(.*)$%', '${1}otzyvy?field=Date&order=Desc', $url);
                $itemReviewsPage = file_get_html($reviewsUrl);
                $commentBlock = $itemReviewsPage->find('div[class=comment]', 0);

                if ($commentBlock)
                    $retVal['LastReviewDate'] = $commentBlock->find('div[itemprop=datePublished]', 0)->innertext;
            }
        }

        return $retVal;

    }

    // Old version:

    /*
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

        return 'https://www.wildberries.ru/catalog/' . $articul . '/otzyvy?field=Date&order=Desc';

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
    */

}

?>