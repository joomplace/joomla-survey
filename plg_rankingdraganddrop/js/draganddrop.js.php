// *** DRAG'and'DROP CODE *** //
var color_cont = '#<?php echo $color_cont ?>';
var color_drag = '#<?php echo $color_drag ?>';
var color_highlight = '#<?php echo $color_highlight ?>';
var last_drag_id = '';
var last_drag_id_drag = '';
var last_drag_quest_n = -1;

function startDrag(e, n){
    last_drag_quest_n = n;
    // determine event object
    if(!e){var e=window.event};
    // determine target element
    var targ=e.target?e.target:e.srcElement;
    //break if not draggable element
    if (targ.id.substring(0, 4) != 'ddiv') {return;}
    if (last_drag_id_drag != '') {
        //break if target not last draggable div(div on mouse)
        if (last_drag_id_drag != targ.id) {return;}
    }
    //Bring draggable div to top, other div's to back
    for (i=1; i<=questions[n].kol_drag_elems; i++) {
        an_div	= sf_getObj('ddiv'+questions[n].cur_quest_id+'_' + i);
        an_div.style.zIndex = 500;
    }
    targ.style.zIndex = 1000;
    //set some config options
    targ.style.position = 'relative';
    last_drag_id = targ.id;
    last_drag_id_drag = targ.id;
    // calculate event X,Y coordinates
    offsetX=e.clientX;
    offsetY=e.clientY;
    // assign default values for top and left properties
    if(!targ.style.left){targ.style.left='0px'};
    if(!targ.style.top){targ.style.top='0px'};
    // calculate integer values for top and left properties
    coordX=parseInt(targ.style.left);
    coordY=parseInt(targ.style.top);
    drag=true;
    questions[n].cont_index = 0;
    // move div element
    document.onmousemove=dragDiv;
}
// continue dragging
function dragDiv(e){
    var n = last_drag_quest_n;
    if(!drag){return};
    if(!e){var e=window.event};
    var targ		= e.target?e.target:e.srcElement;
    //set old coordinates to other div's (because it's position is relative and they 'prygayut'
    if (last_drag_id_drag != '') {
        if (last_drag_id_drag != targ.id) {
            var ddd = sf_getObj(last_drag_id_drag);
            ddd.style.left	= coordX+e.clientX-offsetX+'px';
            ddd.style.top	= coordY+e.clientY-offsetY+'px';
            return;
        }
    }
    if (targ.id.substring(0, 4) != 'ddiv') {return;}
    // move div element
    targ.style.left	= coordX+e.clientX-offsetX+'px';
    targ.style.top	= coordY+e.clientY-offsetY+'px';
    var is_on_cont = false;
    for (i=1; i<=questions[n].kol_drag_elems; i++) {
        an_div			= sf_getObj('cdiv'+questions[n].cur_quest_id+'_' + i);
        FDIV_RightX		= an_div.offsetLeft + an_div.offsetWidth;
        SDIV_LeftX		= targ.offsetLeft;//+coordX+e.clientX-offsetX;
        FDIV_TopY		= an_div.offsetTop;
        FDIV_DownY		= an_div.offsetTop + an_div.offsetHeight;
        SDIV_MiddleY	= targ.offsetTop + parseInt(targ.offsetHeight/2);
        if ( ((parseInt(FDIV_RightX) + 10) > (parseInt(SDIV_LeftX))) &&
            ((parseInt(FDIV_DownY) + 10) > (parseInt(SDIV_MiddleY))) &&
            ((parseInt(FDIV_TopY) + 10) < (parseInt(SDIV_MiddleY))) ) {
            an_div.style.backgroundColor = color_highlight;
            an_div.className = 'jb_survey_dragdrop_left js_dragdrop_highlight';
            questions[n].cont_index = i;
            is_on_cont = true;
        }
        else {
            an_div.style.backgroundColor = color_cont;
            an_div.className = 'jb_survey_dragdrop_left';
        }
    }
    var dr_number = parseInt(last_drag_id.substring(last_drag_id.lastIndexOf("_")+1));
    //! id of the div is 'ddiv_xxx' - five simbols! plus number
    for (i=1; i<=questions[n].kol_drag_elems; i++) {
        if (i != dr_number) {
            an_div			= sf_getObj('ddiv'+questions[n].cur_quest_id+'_' + i);
            if ( (questions[n].coord_left[i]) && (questions[n].coord_left[i] != '') ) {
                an_div.style.left = questions[n].coord_left[i];
            }
            if ( (questions[n].coord_top[i]) && (questions[n].coord_top[i] != '') ) {
                an_div.style.top = questions[n].coord_top[i];
            }
        }
    }
    if (!is_on_cont) { questions[n].cont_index = 0; }
    return false;
}
// stop dragging
function stopDrag(){
    var n = last_drag_quest_n;
    var dr_obj = sf_getObj(last_drag_id);
    if (n < 0) { return; }
    if (dr_obj) {
        var dr_number = parseInt(last_drag_id.substring(last_drag_id.indexOf('_')+1));
        //! id of the div is 'ddivxx_xxx' - ddiv plus quest_id plus '_' plus number
        if (questions[n].cont_index) {
            dr_obj.style.position = 'relative';
            dr_obj.style.left	= '-57px';
            dr_obj.style.top	= parseInt((questions[n].cont_index - 1)*56 - (56*(dr_number - 1)) + 7) + 'px';
            questions[n].ids_in_cont[questions[n].cont_index - 1] = dr_number;
        }

        questions[n].cont_for_ids[dr_number - 1] = questions[n].cont_index;

        questions[n].coord_left[dr_number] = dr_obj.style.left;
        questions[n].coord_top[dr_number] = dr_obj.style.top;
        dr_obj.style.zIndex = 499;
    }
    last_drag_id_drag = '';
    for (i=1; i<=questions[n].kol_drag_elems; i++) {
        an_div	= sf_getObj('cdiv'+questions[n].cur_quest_id+'_' + i);
        an_div.style.backgroundColor = color_cont;
        an_div.className = 'jb_survey_dragdrop_left';
    }
    last_drag_quest_n = -1;
    drag=false;
    check_answer(n);
}
// *** end of DRAG'and'DROP CODE *** //