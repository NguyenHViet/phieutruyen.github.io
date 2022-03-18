<?php
    session_start();


    require_once ('./database/connect_database.php');


    if(!(isset($_GET['comic']) && isset($_GET['chapter']))) {
        header("location: ./");
        die();
    }

    $comic_id = $_GET['comic'];

    $sql = "select chap.id, chap.id_comic, chap.index, chap.name, chap.created_at, chap.updated_at, cm.id_user, cm.name name_cm, count(*) total from chapter chap join comic cm on chap.id_comic = cm.id where chap.id_comic = ".$comic_id." and chap.index = ".$_GET['chapter'];
    $comic = EXECUTE_RESULT($sql);

    if($comic[0]['total'] == 0) {
        header("location: ./");
        die();
    }

    EXECUTE("update comic set total_view = total_view + 1 where id=".$comic_id);

    $user =[];
    
    if(isset($_SESSION['user_id'])) {
        $sql = "select avatar, account_name from user where id = ".$_SESSION['user_id'];
        $user = EXECUTE_RESULT($sql);

        $sql = "insert into readed (id_user, id_chapter) values (".$_SESSION['user_id'].", ".$comic[0]['id'].")";
        EXECUTE($sql);

        $now = time();
    }


    $sql = "select * from chapter chap join comic cm on chap.id_comic = cm.id where chap.id_comic = ".$_GET['comic']." and chap.status='Đã duyệt' order by chap.index asc";
    $chapter = EXECUTE_RESULT($sql);

    $sql = "select * from page pg join chapter chap on pg.id_chapter = chap.id where chap.id_comic = ".$_GET['comic']." and chap.index = ".$_GET['chapter']." order by pg.index asc";
    $page = EXECUTE_RESULT($sql);
     
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Đọc truyện tranh Manga, Manhua, Manhwa, Comic online hay và cập nhật thường xuyên tại PhieuTruyen.Com">
        <meta property="og:site_name" content="PhieuTruyen.Com">
        <meta name="Author" content="PhieuTruyen.Com">
        <meta name="keyword" content="doc truyen tranh, manga, manhua, manhwa, comic">
        <title>Đọc truyện tranh Manga, Manhua, Manhwa, Comic Online</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">
        
        <link rel="stylesheet" type="text/css" href="./css/sidebar.css">
        <link rel="stylesheet" type="text/css" href="./css/footer.css">
        <link rel="stylesheet" type="text/css" href="./css/style-DT.css">
        <link rel="stylesheet" type="text/css" href="./css/breadcrumb.css">
        <link rel="stylesheet" type="text/css" href="./css/topbar.css">
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-kQtW33rZJAHjgefvhyyzcGF3C5TFyBQBA13V1RKPf4uH+bwyzQxZ6CmMZHmNBEfJ" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  

        <script language="javascript">
            function danh_dau_da_doc(){
                $data = "danh-dau-da-doc";
                $.ajax({
                    url : "notification.php",
                    type : "post",
                    dataType:"text",
                    data : {
                        data : $data
                    },
                    success : function (result){
                        $('#notification-button').html(result);
                    }
                });
            }
        </script>
    
    </head>
    <body>
        <?php require_once("./component/header.php"); ?>

        <!-- Thanh công cụ -->
        <div class="sidebar">
            <div class="logo-detail" style="background-color: #B4A5FF;">
                <i class='bx bx-menu' id="btn-menu"></i>
            </div>
            <ul class="nav-list">
                <li>
                    <a href="./">
                        <i class='bx bxs-home'></i>
                        <span class="links_name">Trang chủ</span>
                    </a>
                    <span class="tooltip">Trang chủ</span>
                </li>
                <li>
                    <a href="./typecomic.php">
                        <i class='bx bxs-purchase-tag' ></i>
                        <span class="links_name">Thể loại</span>
                    </a>
                    <span class="tooltip">Thể loại</span>
                </li>
                <li>
                    <a href="./updated.php">
                        <i class='bx bxs-hourglass'></i>
                        <span class="links_name">Mới cập nhật</span>
                    </a>
                    <span class="tooltip">Mới cập nhật</span>
                </li>
                <li>
                    <a href="./following.php">
                        <i class='bx bxs-heart' ></i>
                        <span class="links_name">Theo dõi</span>
                    </a>
                    <span class="tooltip">Theo dõi</span>
                </li>
                <li>
                    <a href="./history.php">
                        <i class='bx bx-history' ></i>
                        <span class="links_name">Lịch sử đọc</span>
                    </a>
                    <span class="tooltip">Lịch sử đọc</span>
                </li>
                <li>
                    <a href="./feedback.php">
                        <i class='bx bx-mail-send' ></i>
                        <span class="links_name">Phản hồi</span>
                    </a>
                    <span class="tooltip">Phản hồi</span>
                </li>
                <li  id="btn-light-dark">
                    <a>
                        <i class='bx bxs-bulb'></i>
                        <span class="links_name">Bật/Tắt đèn</span>
                    </a>
                    <span class="tooltip">Bật/Tắt đèn</span>
                </li> 

                <!-- Nút thao tác khi ở trang đọc truyện -->

                <li>
                    <a href="./comic.php?<?php echo "comic=".$comic_id; ?>">
                        <i class='bx bxs-book'></i>
                        <span class="links_name">Về bìa truyện</span>
                    </a>
                    <span class="tooltip">Về bìa truyện</span>
                </li>
                <li id="trg_trc">
                    <a href="./read.php?<?php echo "comic=".$comic_id."&chapter=".($_GET['chapter'] - 1); ?>">
                        <i class='bx bx-chevrons-left'></i>
                        <span class="links_name">Chương trước</span>
                    </a>
                    <span class="tooltip">Chương trước</span>
                </li>
                <li id="Nav_ListChapBtn">
                    <a>
                        <i class='bx bx-spreadsheet'></i>
                        <span class="links_name">Danh sách chương</span>
                    </a>
                    <span class="tooltip">Danh sách chương</span>
                </li>
                <li id="trg_sau">
                    <a href="./read.php?<?php echo "comic=".$comic_id."&chapter=".($_GET['chapter'] + 1); ?>">
                        <i class='bx bx-chevrons-right' ></i>
                        <span class="links_name">Chương sau</span>
                    </a>
                    <span class="tooltip">Chương sau</span>
                </li>      
            </ul>

            <?php
                if($_GET['chapter'] <= 1){
                    echo '<style>#trg_trc{display: none;}</style>';
                }
                if($_GET['chapter'] >= count($chapter)){
                    echo '<style>#trg_sau{display: none;}</style>';
                }
            ?>
            <div class="Nav_ListChap">
                <ul>
                    <?php
                        foreach($chapter as $item) {
                            echo '<li> <a href="./read.php?comic='.$comic_id.'&chapter='.$item['index'].'"> <span class="links_name"';
                            if($item['index'] == $_GET['chapter']) echo ' style="font-weight: bold;"';
                            echo '>Chương '.$item['index'].'</span> </a> </li>';
                        }
                    ?>
                </ul>
            </div>
        </div>

        <script>
            $("#btntop").click(function () {
                $("html").animate({
                    scrollTop:0
                }, 750);
            })
        </script>

        <!--Khối tiêu đề đầu trang, dùng để đổi server ảnh-->
        <div class="container-xxl" id="read-story-info">
            <!-- Thanh breadcrumb --> 
            <div class="contain_nav_breadvrumb">
                <nav  class="nav_breadcrumb" aria-label="Page breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" aria-current="page"><i class='bx bxs-home'></i></li>
                        <li class="breadcrumb-item"><?php echo $comic[0]['name_cm']; ?></li>
                        <li class="breadcrumb-item active">Chap <?php echo $_GET['chapter']." - ".$comic[0]['name']; ?></li>
                    </ol>
                </nav>
            </div>
            <!--  -->
            <div class="caption">
                <a id="story-title"><?php echo $comic[0]['name_cm']; ?></a>
            </div>
            <div class="caption">
                <a style="font-size: .75em;" id="story-chapter">Chap <?php echo $_GET['chapter']; ?></a>
            </div>
            <p style="font-size: 1em; text-align: center;">Nếu không xem được ảnh vui lòng chọn server khác dưới đây</p>
            <div id="server-option">
                <a class="btn btn-primary" href="#" role="button">Server 1</a>
                <a class="btn btn-primary" href="#" role="button">Server 2</a>
                <a class="btn btn-primary" href="#" role="button">Server 3</a>
            </div>
        </div>

        <!--Hình truyện đọc-->
        <div  id="contentDT">
            <?php
                foreach($page as $item) {
                    echo '<img src="'.$item['link_page'].'" alt="ayame">';
                }
            ?>
        </div>

        <?php require_once("./component/comment.php"); ?>
         
        <?php $btntop = false; require_once("./component/footer.php"); ?>

        <script language="javascript" src="./js/jsheader.js"></script>
        <script language="javascript" src="./js/sidebarType2.js"></script>
    </body>
</html>