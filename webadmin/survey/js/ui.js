$(document).ready(function(){
    $('#question-list-table').sortable({
        containerSelector: "table.question-sortable",
        itemPath: "> tbody",
        itemSelector: "tr.question-list-row",
        placeholder: "<tr class='placeholder'/>",
        handle: " td.position-mover",

        // animation on drop
        onDrop: function  ($item, container, _super) {
            var $clonedItem = $('<li/>').css({height: 0});
            $item.before($clonedItem);
            $clonedItem.animate({'height': $item.height()});

            $item.animate($clonedItem.position(), function  () {
                $clonedItem.detach();
                _super($item, container);
            });

            // Sort position// Load components
            /*if (document.getElementById("question-choice-data")) {
                var container = document.getElementById("question-choice-data");
                var data = container.getElementsByClassName("question-choice");
                var loader = document.getElementById("multiple-answer-loader");

                var i = 0;
                while(i < data.length) {

                    var input_type = data[i].querySelector("input[name='input_type']").value;
                    var value = data[i].querySelector("input[name='value']").value;
                    var open = data[i].querySelector("input[name='open']").value;
                    var checked = data[i].querySelector("input[name='checked']").value;
                    var type = (document.getElementById("question-type-single").checked == true) ? "single" : "multiple";

                    loader.appendChild(getMultipleChoiceComponent(type, i + 1, input_type, value, open, checked));

                    i++;
                }

                bindAnswerSortable();
            } */

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

        },

        // set $item relative to cursor position
        onDragStart: function ($item, container, _super) {
            var offset = $item.offset(),
                pointer = container.rootGroup.pointer;

            adjustment = {
                left: pointer.left - offset.left,
                top: pointer.top - offset.top
            };

            _super($item, container);
        },

        onDrag: function ($item, position) {
            $item.css({
                left: position.left - adjustment.left,
                top: position.top - adjustment.top
            });
        }
    });

    $('.based-question-list-table').sortable({
        containerSelector: "table.question-sortable",
        itemPath: "> tbody",
        itemSelector: "tr.question-list-row",
        placeholder: "<tr class='placeholder'/>",
        handle: " td.position-mover",

        // animation on drop
        onDrop: function  ($item, container, _super) {
            var $clonedItem = $('<li/>').css({height: 0});
            $item.before($clonedItem);
            $clonedItem.animate({'height': $item.height()});

            $item.animate($clonedItem.position(), function  () {
                $clonedItem.detach();
                _super($item, container);
            });

            var pos = 1;

            var data = [];

            $item.parent().find(".question-list-row").each(function(){
                $(this).find(".position-display").text(pos);

                var _data = [];
                _data[0] = parseInt($(this).find("input[name='question_id']").val());
                _data[1] = pos;
                _data[2] = parseInt($(this).find("input[name='based']").val());

                data.push(_data);

                pos++;
            });

            $.ajax({
                url : "/webadmin/survey/phpajax/sort_question.php",
                type : "post",
                data : {data: data}
            });

        },

        // set $item relative to cursor position
        onDragStart: function ($item, container, _super) {
            var offset = $item.offset(),
                pointer = container.rootGroup.pointer;

            adjustment = {
                left: pointer.left - offset.left,
                top: pointer.top - offset.top
            };

            _super($item, container);
        },

        onDrag: function ($item, position) {
            $item.css({
                left: position.left - adjustment.left,
                top: position.top - adjustment.top
            });
        }
    });

    // Load components
    if (document.getElementById("question-choice-data")) {
        var container = document.getElementById("question-choice-data");
        var data = container.getElementsByClassName("question-choice");
        var loader = document.getElementById("multiple-answer-loader");

        var i = 0;
        while(i < data.length) {

            var input_type = data[i].querySelector("input[name='input_type']").value;
            var value = data[i].querySelector("input[name='value']").value;
            var open = data[i].querySelector("input[name='open']").value;
            var checked = data[i].querySelector("input[name='checked']").value;
            var type = (document.getElementById("question-type-single").checked == true) ? "single" : "multiple";

            loader.appendChild(getMultipleChoiceComponent(type, i + 1, input_type, value, open, checked));

            i++;
        }

        bindAnswerSortable();
    }
});

function deleteQuestion(link, question_id, question, based) {
    var r = confirm("You are deleteing Question: " + question + ". This will delete all answers relating to this question too.");
    if (r == true) {
        $.ajax({
            url: "/webadmin/survey/phpajax/delete_question.php",
            method: "post",
            data: {question_id: question_id, based: based},
            beforeSend: function(){
                link.innerHtml = "Deleting";
            },
            success: function(s) {
                if (s == "error") {
                    alert("There is some internal issue during deleting this question. Please try again.");
                } 

                location.reload();
            }
        });
    }
}

function getMultipleChoiceComponent(type, id, input_type, value, open, checked) {

    // Default values
    input_type = (input_type == "") ? "text" : input_type;

    // Create remove button
    var removeBtn = document.createElement("button");
    removeBtn.setAttribute("class", "btn");
    removeBtn.setAttribute("type", "button");
    removeBtn.innerHTML = "Remove";		

    // Create type selector
    var typeSelector = document.createElement("select");
    typeSelector.setAttribute("name", "question[question_choice]["+ id +"][input_type]");
    
    var imageTypeOption = document.createElement("option");
    imageTypeOption.setAttribute("value", "image");

    if (input_type == "image") {
        imageTypeOption.setAttribute("selected", "selected");
    }

    imageTypeOption.innerHTML = "Image Input";

    var textTypeOption = document.createElement("option");
    textTypeOption.setAttribute("value", "text");

    if (input_type == "text") {
        textTypeOption.setAttribute("selected", "selected");
    }

    textTypeOption.innerHTML = "Text Input";

    typeSelector.appendChild(textTypeOption);
    //typeSelector.appendChild(imageTypeOption); // TODO: Image Input Type

    // Create image input
    var imageInput = document.createElement("input");
    imageInput.setAttribute("type", "file");
    imageInput.setAttribute("name", "question_choice_img["+ id +"]");

    // Create text input
    var textInput = document.createElement("input");
    textInput.setAttribute("type", "text");
    textInput.setAttribute("required", "required");
    textInput.setAttribute("name", "question[question_choice]["+ id +"][value]");
    textInput.style.width = "98%";
    textInput.value = value;

    // Create open selector
    var openSelector = document.createElement("input");
    openSelector.setAttribute("type", "checkbox");
    openSelector.setAttribute("name", "question[question_choice]["+ id +"][open]");
    openSelector.value = "1";

    if (open == 1) {
        openSelector.checked = true;
    }

    // Create open selector label
    var openSelectorLabel = document.createElement("span");
    openSelectorLabel.innerHTML = "Open Answer";

    // Create default selector
    var defaultSelector = document.createElement("input");
    defaultSelector.setAttribute("type", "checkbox");
    defaultSelector.setAttribute("class", type + "-checkbox default-selector");
    defaultSelector.setAttribute("name", "question[question_choice]["+ id +"][checked]");
    defaultSelector.value = "1";

    if (checked == 1) {
        defaultSelector.checked = true;
    }

    if (type == "single") {
        defaultSelector.setAttribute("onclick", "behaveSingleCheck(this);");
    }

    // Create default selector label
    var defaultSelectorLabel = document.createElement("span");
    defaultSelectorLabel.innerHTML = "Checked by Default";

    // Row of table
    var tr = document.createElement("tr");
    tr.setAttribute("class", "answer-list-row position-mover");

    // Columns of table
    var btnCol = document.createElement("td");
    btnCol.style.width = "11%";
    btnCol.appendChild(removeBtn);

    var typeSelectorCol = document.createElement("td");
    typeSelectorCol.appendChild(typeSelector);
    typeSelectorCol.style.width = "11%";

    var inputCol = document.createElement("td");
    inputCol.appendChild(textInput);

    var openSelectorCol = document.createElement("td");
    openSelectorCol.appendChild(openSelector);
    openSelectorCol.appendChild(openSelectorLabel);
    openSelectorCol.style.width = "11%";

    var defaultSelectorCol = document.createElement("td");
    defaultSelectorCol.appendChild(defaultSelector);
    defaultSelectorCol.appendChild(defaultSelectorLabel);
    defaultSelectorCol.style.width = "15%";

    tr.appendChild(typeSelectorCol);
    tr.appendChild(inputCol);
    tr.appendChild(openSelectorCol);
    tr.appendChild(defaultSelectorCol);
    tr.appendChild(btnCol);

    // Actions

    removeBtn.addEventListener("click", function(){
        document.getElementById("multiple-answer-loader").removeChild(tr);
    })

    return tr;

}

function addNewAnswer() {
    var loader = document.getElementById("multiple-answer-loader");
    var id = loader.childNodes.length + 1;
    var questionType = document.querySelector('input[name="question[type]"]:checked').value;

    loader.appendChild(getMultipleChoiceComponent("single", id, "text", "", 0, 0));
}

function loadSingleAnswer() {

    document.getElementById("multiple-answer-container").removeAttribute("style");

    var loader = document.getElementById("multiple-answer-loader");
    var id = loader.childNodes.length;

    if (id > 0) {
        var i = 0;
        
        var checkbox = loader.getElementsByClassName("default-selector");

        while(i < checkbox.length) {
            checkbox[i].checked = false;
            checkbox[i].setAttribute("onclick", "behaveSingleCheck(this);");
            checkbox[i].classList.remove("multiple-checkbox");
            checkbox[i].classList.add("single-checkbox");

            i++;
        }

    } else {
        loader.appendChild(getMultipleChoiceComponent("single", 1, "text", "", 0, 0));
    }
}

function loadMultipleAnswer() {

    document.getElementById("multiple-answer-container").removeAttribute("style");

    var loader = document.getElementById("multiple-answer-loader");
    var id = loader.childNodes.length;

    if (id > 0) {
        var i = 0;

        var checkbox = loader.getElementsByClassName("default-selector");
        
        while(i < checkbox.length) {
            console.log(checkbox[i]);
            
            checkbox[i].checked = false;
            checkbox[i].setAttribute("onclick", "");
            checkbox[i].classList.remove("single-checkbox");
            checkbox[i].classList.add("multiple-checkbox");
            i++;
        }

    } else {
        loader.appendChild(getMultipleChoiceComponent("multiple", 1, "text", "", 0, 0));
    }
}

function removeAllMultipleAnswer() {
    var loader = document.getElementById("multiple-answer-loader");
    while(loader.firstChild) {
        loader.removeChild(loader.firstChild);
    }

    document.getElementById("multiple-answer-container").style.display = "none";
}

function checkConfirm(msg) {
    var thereIsAnswer = false;

    $("#multiple-answer-loader").find("input[type='text']").each(function(){
        if($(this).val().length > 0) {
            thereIsAnswer = true;
        }
    });

    if (thereIsAnswer) {
        if (confirm(msg) === false) { 
            return false; 
        }
    }

    return true;
}

function behaveSingleCheck(checkbox) {
    if (checkbox.checked) {
        var singleCheckbox = document.getElementById("multiple-answer-loader").getElementsByClassName("single-checkbox");
        var i = 0;
        while(i < singleCheckbox.length) {
            singleCheckbox[i].checked = false;
            i++;
        }
        checkbox.checked = true;
    }
}

function bindAnswerSortable() {
    $('#answer-sortable-table').sortable({
        containerSelector: "table.sortable-table",
        itemPath: "> tbody",
        itemSelector: "tr.answer-list-row",
        placeholder: "<tr class='placeholder'/>",
        handle: " .position-mover",

        // animation on drop
        onDrop: function  ($item, container, _super) {
            var $clonedItem = $('<li/>').css({height: 0});
            $item.before($clonedItem);
            $clonedItem.animate({'height': $item.height()});

            $item.animate($clonedItem.position(), function  () {
                $clonedItem.detach();
                _super($item, container);
            });
        },

        // set $item relative to cursor position
        onDragStart: function ($item, container, _super) {
            var offset = $item.offset(),
                pointer = container.rootGroup.pointer;

            adjustment = {
                left: pointer.left - offset.left,
                top: pointer.top - offset.top
            };

            _super($item, container);

        },

        onDrag: function ($item, position) {
            $item.css({
                left: position.left - adjustment.left,
                top: position.top - adjustment.top
            });
        }
    });
}