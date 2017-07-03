<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name=viewport content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Imap</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.1.1/jquery-confirm.min.css">
    <link rel="stylesheet" href="css/checkbox-star.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
        <div class="row">
            <div class="col-sm-3 col-md-2">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapse-menu" data-toggle="collapse"  data-target="#myNavbar">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <div class="navbar-toggle glyph-style-collapse arrow-group pull-right" data-toggle="collapse">
                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span></button>
                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-chevron-right"></span></button>
                    </div>
                    <a class="navbar-brand" href="#"><span class="gmail-logo">Gmail</span></a>
                </div>
            </div>
            <div class="collapse navbar-collapse" id="myNavbar">
                <div class="col-sm-6 col-md-7">
                    <input type="checkbox" name="SelectAll" id="js-select-all" class="hidden-xs" role="menu">
                    <div class="btn-group navbar-btn hidden-xs glyph-style">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="glyphicon glyphicon-sort-by-attributes"></span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href="#" class="js-all-email">All</a></li>
                                <li><a href="#" class="js-read-email">Read</a></li>
                                <li><a href="#" class="js-unread-email">Unread</a></li>
                                <li><a href="#" class="js-stared-email">Stared</a></li>
                            </ul>
                        </div>
                        <button type="button" class="btn btn-default" id="reload"><span class="glyphicon glyphicon-repeat"> </span></button>
                        <button type="button" class="btn btn-default js-delete-select-msg"><span class="glyphicon glyphicon-trash"> </span></button>

                    </div>
                    <!--visible-xs-->
                    <ul class="nav navbar-nav collapse-nav">
                        <li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in" ><button class="btn btn-danger btn-group btn-group-justified text-uppercase " data-toggle="modal" data-target=".bs-example-modal-lg">compose</button></a></li>
                       <div class="js-collapse-menu"><li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified" >INBOX<span class="badge pull-right" id="inbox-num">8</span></button></a></li>
                        <li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified" >Allmail</button></a></li>
                        <li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified" >Drafts</button></a></li>
                        <li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified" >Important</button></a></li>
                        <li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">Sentmail</button></a></li>
                        <li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">Spam</button></a></li>
                        <li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">Starred<span class="badge pull-right" id="spam-num">3</span></button></a></li>
                        <li class="visible-xs js-collapse-menu-li"><a href="#" class='button-menu'data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">Trash</button></a></li></div>
                        <li class="dropdown">
                            <a class="dropdown-toggle visible-xs button-menu" data-toggle="dropdown" href="#"><button class="btn btn-default btn-group-justified">Show me <span class="caret"></span></button></a>
                            <ul class="dropdown-menu">
                                <li><a href="#" class="js-all-email small-button-menu" data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">All</button></a></li>
                                <li><a href="#" class="js-read-email small-button-menu" data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">Read</button></a></li>
                                <li><a href="#" class="js-unread-email small-button-menu" data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">Unread</button></a></li>
                                <li><a href="#" class="js-stared-email small-button-menu" data-toggle="collapse" data-target=".navbar-collapse.in"><button class="btn btn-default btn-group-justified">Stared</button></a></li>
                            </ul>
                        </li>
                        <li class="visible-xs"><a class="button-menu" data-toggle="collapse" data-target=".navbar-collapse.in" href="#"><button class="btn btn-default btn-group-justified">Delete</button></a></li>
                    </ul>
                    <!--visible-xs-->
                </div>
                <div class="col-sm-3 col-md-3">
                    <!--hidden-xs-->
                    <div class="btn-group navbar-btn glyph-style hidden-xs">
                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-chevron-left"></span></button>
                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-chevron-right"></span></button>
                    </div>
                    <!--hidden-xs-->
                </div>
            </div>
        </div>
    </div>
</nav>
<div class="container">
    <div class="row">
        <div class="col-sm-3 col-md-2 hidden-xs">
            <div class="left-nav-menu">
                <button class="btn btn-danger btn-group btn-group-justified text-uppercase " data-toggle="modal" data-target=".bs-example-modal-lg">compose</button>
                <nav class="left-navbar">
                    <ul class="nav nav-pills nav-stacked js-active navbar-space">
                        <li class="js-inbox"><a href="#">INBOX</a></li>
                        <li><a href="#">Allmail</a></li>
                        <li><a href="#">Drafts</a></li>
                        <li><a href="#">Important</a></li>
                        <li><a href="#">Sentmail</a></li>
                        <li><a href="#">Spam</a></li>
                        <li><a href="#">Starred</a></li>
                        <li><a href="#">Trash</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <div class="col-sm-9 col-md-10">
            <table class="table table-hover ">
                <tbody class='js-show-hide'>

                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<!--  modal window -->

<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" data-backdrop="static" id="myModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form class="js-form" action="mail/mailer.php" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" id="closebtn">&times;</button>
                    <h4 class="modal-title text-uppercase">new message</h4>
                </div>
                <form class="js-form " action="#" method="POST">
                    <div class="modal-body">
                        <div class="input-group">
                            <span class="input-group-addon">@</span>
                            <input id="email" name="email" type="text" class="form-control" placeholder="Recipients">
                        </div>
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            <input id="subject" type="text" class="form-control" name="subject" placeholder="Subject" >
                        </div>
                        <br />
                        <br />
                        <div class="form-group">
                            <label for="comment">Text message: </label>
                            <textarea id="message" name="message" class="form-control" rows="10" id="comment"></textarea>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <div id="loader" class="loader hide"></div>
                    <span class="sending hide">Sending email...</span>
                    <button type="submit" class="btn btn-primary"  id="Send">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>




<!-- Modal -->
<div class="modal fade" id="myModal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title text-uppercase" id="myModalLabel">Message</h4>
            </div>
            <div class="modal-body js-open-msg">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal text message-->


<!-- Modal status Send-->
<div id="modalStatusSend" class="modal fade bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <span class="status-send" ></span>
        </div>
    </div>
</div>
<!-- Modal status Send-->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.1.1/jquery-confirm.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/mouse0270-bootstrap-notify/3.1.7/bootstrap-notify.min.js"></script>
<script src="{{asset('js/main.js')}}"></script>
</body>
</html>
