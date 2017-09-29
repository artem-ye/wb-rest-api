<?php

include_once('simple_html_dom.php');

class WB_ReviewParser
{

    function GetItemReviews($articul) {

        $url  = $this->BuildReviewURL($articul);
        $html = file_get_html($url);
        return $this->ParseReviews($html);

    }

    function BuildReviewURL($articul) {

        return 'https://www.wildberries.ru/catalog/' . $articul . '/otzyvy?field=Date&order=Asc';

    }

    private function ParseReviews($html) {

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