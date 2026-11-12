/*
    title: static-pagination.js
    description: Pagination for static pages based on bootstrap and jquery (eg. jekyll)
    author: Incio
    github: https://github.com/inci-o/static-pagination
    site: https://inci-o/github.io
    email: inciojs@gmail.com
    version: 0.1.0
 */


let _currentPage, _perPage, _showPage, _paginationLength, $staticPaginationList;

let $list = $('#static-pagination-list');
let $pagination = $('#static-pagination');
function staticPagination(userList, userOptions) {
    $staticPaginationList = userList;

    if (userOptions.perPage != null) {
        _perPage = userOptions.perPage;
    } else {
        _perPage = 10;
    }

    if (userOptions.showPage != null) {
        _showPage = userOptions.showPage;
    } else {
        _showPage = 10;
    }
    _paginationLength = parseInt($staticPaginationList.length / _perPage) + 1;
    _staticPagination(1);
}


var modal = document.getElementById("myModal");

// Get the button that opens the modal
var btn = document.getElementById("myBtn");

// Get the <span> element that closes the modal
var span = document.getElementsByClassName("close")[0];

// When the user clicks the button, open the modal 
// btn.onclick = function() {
//   modal.style.display = "block";
// }

// When the user clicks on <span> (x), close the modal
// span.onclick = function() {
//   modal.style.display = "none";
// }

// window.onclick = function(event) {
//     if (event.target == modal) {
//       modal.style.display = "none";
//     }
//   }

// When the user clicks anywhere outside of the modal, close it



//function _staticModal(txt){

   // alert( $staticPaginationList[txt].content );
    // Show the modal.
  //  jQuery(exampleModal).modal('show');
  
  //}
  
 // var htmdata = $('<div id="myModal" class="modal"><div class="modal-content"><span class="close">&times;</span> <p>Some text in the Modal..</p></div></div>');
// $('body').append(htmdata);
//alert(htmdata)



var html;
var titletxt;

function _staticModal(txt){
   // alert( $staticPaginationList[txt].content );
  
   $('.popup-window').show();
  html= $staticPaginationList[txt].content ;
  titletxt=$staticPaginationList[txt].title
   $('.popup-dv p').append(html)
   $('.popup-dv h3').append(titletxt)
    // var html =  '<div id="myModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirm-modal" aria-hidden="true">';
    // html += '<div class="modal-dialog">';
    // html += '<div class="modal-content">';
    // html += '<div class="modal-header">';
    // html += '<a class="close" data-dismiss="modal">×</a>';
    // html += '<h4>'+$staticPaginationList[txt].title +'</h4>'
    // html += '</div>';
    // html += '<div class="modal-body">';
    // html += $staticPaginationList[txt].content ;
    // html += '</div>';
    // html += '<div class="modal-footer">';
    // html += '<span class="btn btn-primary" data-dismiss="modal">Close</span>';
    // html += '</div>';  
    // html += '</div>';  
    // html += '</div>';  
    // html += '</div>';  
    // $('#myModal').append(html);
 //alert(html)
   //document.getElementsByTagName('body').appendChild(html)
   // alert(html)
   //console(log)
    // $("#dynamicModal").modal();
    // $("#dynamicModal").modal('show');

    // $('#dynamicModal').on('hidden.bs.modal', function (e) {
    //     $(this).remove();
    //});
     

    // var html ='<div id="myModal" class="modal">';
    //  html += ' <div class="modal-content">';
    //  html += ' <span class="close">&times;</span>';
    //  html += '<p> '+ $staticPaginationList[txt].content +'</p>' ;
    //  html += '</div>';
     
    //   $('body').append(html);


//     var bodyapp ='<div class="modal-dialog " role="document">' +
//         '<div class="modal-content">' +
//         '<div class="modal-header">' +
//         '<h5 class="modal-title"></h5>' +
//         '<button type="button" class="close" data-dismiss="modal" aria-label="Close">' +
//         '<span aria-hidden="true">&times;</span>' +
//         '</button>' +
//         '</div>' +
//         '<div class="modal-body">' + $staticPaginationList[txt].content +'</div>' +
//         '<div class="modal-footer"></div>' +
//         '</div>' +
//         '</div>'

// alert(bodyapp)
       /// var bodyapp = document.body[0].append(bodyapp).html();
       // alert("bodyapp  "+bodyapp)
        //$(body).append(bodyapp)
     //   alert($('body').append(bodyapp).t)
   // $.showModal({title: "Hello World!", body: $staticPaginationList[txt].content })
}
//new BstrapModal().Show();
var BstrapModal = function (title, body, buttons) {
    var title = title || "Lorem Ipsum History", body = body || "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.", buttons = buttons || [{ Value: "CLOSE", Css: "btn-primary", Callback: function (event) { BstrapModal.Close(); } }];
    var GetModalStructure = function () {
        var that = this;
        that.Id = BstrapModal.Id = Math.random();
        var buttonshtml = "";
        for (var i = 0; i < buttons.length; i++) {
            buttonshtml += "<button type='button' class='btn " + (buttons[i].Css||"") + "' name='btn" + that.Id + "'>" + (buttons[i].Value||"CLOSE") + "</button>";
        }
        return "<div class='modal fade' name='dynamiccustommodal' id='" + that.Id + "' tabindex='-1' role='dialog' data-backdrop='static' data-keyboard='false' aria-labelledby='" + that.Id + "Label'><div class='modal-dialog'><div class='modal-content'><div class='modal-header'><button type='button' class='close modal-white-close' onclick='BstrapModal.Close()'><span aria-hidden='true'>&times;</span></button><h4 class='modal-title'>" + title + "</h4></div><div class='modal-body'><div class='row'><div class='col-xs-12 col-md-12 col-sm-12 col-lg-12'>" + body + "</div></div></div><div class='modal-footer bg-default'><div class='col-xs-12 col-sm-12 col-lg-12'>" + buttonshtml + "</div></div></div></div></div>";
}();
    BstrapModal.Delete = function () {
        var modals = document.getElementsByName("dynamiccustommodal");
        if (modals.length > 0) document.body.removeChild(modals[0]);
    };
    BstrapModal.Close = function () {
        $(document.getElementById(BstrapModal.Id)).modal('hide');
        BstrapModal.Delete();
    };    
    this.Show = function () {
        BstrapModal.Delete();
        document.body.appendChild($(GetModalStructure)[0]);
        var btns = document.querySelectorAll("button[name='btn" + BstrapModal.Id + "']");
        for (var i = 0; i < btns.length; i++) {
            btns[i].addEventListener("click", buttons[i].Callback || BstrapModal.Close);
        }
        $(document.getElementById(BstrapModal.Id)).modal('show');
    };
};
function _staticPagination(page) {
   if (page === 0 && _currentPage !== 1) {
        // 이전 (previous)
        _staticPagination(_currentPage - 1);
    } else if (page === _paginationLength + 1 && _currentPage !== _paginationLength + 1) {
        // 다음 (next)
        _staticPagination(_currentPage + 1);
    } else {
        // 일반 선택 (normal select)
        _currentPage = page;
        $list.empty();
        for (let i = _currentPage * _perPage - _perPage; i < _currentPage * _perPage; i++) {
            try {
                // list 추가, example 확인 (list append, Please refer to example for this)
                $list.append('' +
                    '<li class="div_area1" onclick="_staticModal('+i + '); " data-toggle="modal" data-target="#myModal">' +                    
                    '<img src=' + $staticPaginationList[i].url + ' ' + 'class="img-fluid">' +
                    '<p>' + $staticPaginationList[i].title + '</p>' +
                   '</li>');
            } catch (exception) {
                break;
            }
        }
    }

    if (_paginationLength > 1) {
        $pagination.empty();
        if (_currentPage === 1) {
            $pagination.append('<li class="page-item disabled">' +
                '<a class="page-link" href="javascript:void(0);" onclick="_staticPagination(' + 0 + ');">' +
                '<img src="assets/images/alumni/back.png" class="img-fluid"/>' +
                '</a>' +
                '</li>');
        } else {
            $pagination.append('<li class="page-item">' +                
                '<a class="page-link" href="javascript:void(0);" onclick="_staticPagination(' + 0 + ');">' +
                '<img src="assets/images/alumni/back.png" class="img-fluid"/>' +
                '</a>' +
                '</li>');
        }

        for (let _page = 1; _page <= _paginationLength; _page++) {
            if (_page === _currentPage) {
                $pagination.append('<li class="page-item active">' +                  
                    '<a class="page-link" href="javascript:void(0);" onclick="_staticPagination(' + _page + ');">' + _page + '</a>' +
                    '</li>');
            } else {
                $pagination.append('<li class="page-item">' +
                    '<a class="page-link" href="javascript:void(0);" onclick="_staticPagination(' + _page + ');">' + _page + '</a>' +
                    '</li>');
            }
        }

        if (_currentPage === _paginationLength) {
            $pagination.append('<li class="page-item disabled">' +
                '<a class="page-link" href="javascript:void(0)" onclick="_staticPagination(' + (_paginationLength + 1) + ');">' +
                '<img src="assets/images/alumni/next.png" class="img-fluid"/>' +
                '</a>' +
                '</li>');
        } else {
            $pagination.append('<li class="page-item">' +
                '<a class="page-link" href="javascript:void(0)" onclick="_staticPagination(' + (_paginationLength + 1) + ');">' +
                '<img src="assets/images/alumni/next.png" class="img-fluid"/>' +
                '</a>' +
                '</li>');
        }
    }
}
