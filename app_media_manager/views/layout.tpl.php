<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-COMPATIBLE" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Media Archive</title>

    <?php echo $this->fetch('head/css.tpl.php'); ?>
    <?php echo $this->fetch('head/js.tpl.php'); ?>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#mm-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="#">&nbsp;</a>
            </div>

            <div class="collapse navbar-collapse" id="mm-navbar-collapse-1">
                <?php echo $this->fetch('layout/mainnav.tpl.php'); ?>

                <form class="navbar-form navbar-right" role="search" action="<?php echo $this->manager->friendlyAction("search"); ?>" method="get">
                    <div class="form-group">
                        <input type="text" class="form-control" name="terms" id="browseTerms" placeholder="Search" />
                    </div>
                    <button type="submit" class="btn btn-default">Submit</button>
                </form>

                <ul class="nav navbar-nav navbar-right">
                    <?php if($this->auth->isAuth()): ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button"><?php echo $this->auth->getSession()->fullname(); ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li>Role: <?php echo $this->auth->getSession()->type()->title; ?></li>
                            <li class="divider"></li>
                            <li>
                                <a href="<?php echo $this->manager->friendlyAction("cart"); ?>">View cart</a>
                            </li>
                            <li>
                                <a href="<?php echo $this->manager->friendlyAction("auth", "logout"); ?>">Logout</a>
                            </li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="<?php echo $this->manager->friendlyAction('auth'); ?>">Log In</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php echo $this->contentforlayout; ?>
    </div>

    <!-- <footer class="footer navbar navbar-fixed-bottom">
        <div class="container">
            <p>FOOTER CONTENT HERE</p>
        </div>
    </footer> -->
</body>
</html>
