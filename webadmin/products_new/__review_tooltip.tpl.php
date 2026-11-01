<ul style="white-space:nowrap;">
<li>Total Reviews: <?php hecho($item_reviews['total_reviews'])?></li>
<li>Average Rating: <?php hecho(number_format($item_reviews['average_rating'],1))?></li>
<li>Last Reviewed: <?php hecho(date('d/m/Y',strtotime($item_reviews['latest_review'])))?></li>
</ul>