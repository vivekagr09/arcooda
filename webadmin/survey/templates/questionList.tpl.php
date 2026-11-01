<?php

/**
 *	Template: questionList
 *	Page: editSurvey
 *
 *	The template displays the list of question for the survy.
 *
 *  Precondition: 
 *
 *	Postcondition: Output HTML
 *
 *	@author Kelvin <khtat@highwaygroup.net>
 */

?>

<!-- View Survey Title -->
<br>
<div class="flagHeader">
	<span>Questions</span>
	<a href="index.php?action=add_question&survey_id=<?= $id ?>" class="sort-link">Add Question</a>
</div>
<br>

<!-- List of survey -->
<table class="view-table question-sortable sortable-table" id="question-list-table">
	<thead>
		<tr>
			<th>No.</th>
			<th>Question</th>
			<th>Results</th>
			<th></th>
		</tr>
	</thead>
	<tbody id="question_table" name="question_table">
		<?php foreach($surveyQuestions as  $count=>$question) { ?>

		<tr class="question-list-row" id="question_row[<?= $count ?>]">
			<td class="position-mover"><span class="position-display"><?= $question['position'] ?></span>
		<input type="hidden" name="question_rank[<?= $count ?>]" value="<?= $count ?>">
		<a href="#"  href="#" onclick="rankQuestion(<?= $count ?>, -1); return false" >
            <img src="/webadmin/images/up_arrow.gif" alt="Move Up" />
        </a>
        <a href="#" onclick="rankQuestion(<?= $count ?>, 1); return false" >
            <img src="/webadmin/images/down_arrow.gif" alt="Move Down" />
        </a>
		</td>
			<td><?= $question['question'] ?></td>
			<td>0</td>
			<td>
				<a href="index.php?action=edit_question&id=<?= $question['id'] ?>">Edit</a>
				<a href="#" onclick="deleteQuestion(this, '<?= $question['id'] ?>', '<?= $question['question'] ?>', false); return false;">Delete</a>

				<!-- Question ID for sorting. -->
				<input type="hidden" name="question_id" value="<?= $question['id'] ?>" />
			</td>
		</tr>

		<?php } // #FOREACH ?>
	</tbody>
</table>
<script type="text/javascript">
	function searchArray(needle,haystack)
	{
		if(needle==null || haystack==null) return null;
		for(var i=0;i<haystack.length;i++)
			if(needle==haystack[i])
				return i;
		return null;
	}
	function getQuestionRows() {
        var product_table = getElement("question_table");
        var product_rows = [];
        if (product_table != null) {
            var theRows = product_table.rows;
            for (var i = 0; i < theRows.length; i++)
                if (theRows[i].id.substr(0, 12) == 'question_row')
                    if (searchArray(theRows[i].id.substr(12), product_rows) == null)
                        product_rows[product_rows.length] = theRows[i].id.substr(12);
        }
        return product_rows;
    }
    function rankQuestion(row, direction) {
        var from_row = '[' + row + ']';
        var from_rank = Number(getValue("question_rank" + from_row));
        console.log(from_rank);
        var swap_row = null;
        var swap_rank = null;
        var product_rows = getQuestionRows();
        for (var i = 0; i < product_rows.length; i++) {
            var check_row = product_rows[i];
            var check_rank = Number(getValue("question_rank" + check_row));
            if (check_row != from_row && ((direction < 0 && check_rank <
	                    from_rank && (swap_rank == null || check_rank > swap_rank)
	                    ) || (direction > 0 && check_rank > from_rank && (
	                    swap_rank == null || check_rank < swap_rank)))) {
	            swap_row = check_row;
	            swap_rank = check_rank;
	        }
	    }
        if (swap_row != null) {
            setValue("question_rank" + from_row, swap_rank);
            setValue("question_rank" + swap_row, from_rank);
            if (direction > 0) {
                var temp = from_row;
                from_row = swap_row;
                swap_row = temp;
            }
            var source_row;
            var target_row = getElement("question_row" + swap_row);
            var target_table = target_row.parentNode;
            if (source_row = getElement("question_row" + from_row)) target_table.insertBefore(source_row, target_row);
            var pos = 1;

            var data = [];

            $("#question-list-table").find(".question-list-row").each(function(){
                $(this).find(".position-display").text(pos);

                var _data = [];
                _data[0] = parseInt($(this).find("input[name='question_id']").val());
                _data[1] = pos;

                data.push(_data);

                pos++;
            });

            $.ajax({
                url : "/webadmin/survey/phpajax/sort_question.php",
                type : "post",
                data : {data: data}
            });
        }
    }
</script>