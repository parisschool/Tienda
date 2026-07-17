<header id="topnav">
    <div class="topbar-main">
        <div class="container active">

            <!-- LOGO -->
            <div class="topbar-left">
                <a href="<?=base_url('')?>" class="logo">
                    <span><img src="<?=base_url('template/')?>assets/images/logo-1.png" alt="logo" style="height: 44px;"></span>
                </a>
            </div>
            <!-- End Logo container-->

            <div class="navbar-custom navbar-left">
                <div id="navigation">
                    <ul class="navigation-menu">
                        <?php foreach ($menus as $menu) {
                            $children = (isset($menu->children) && is_array($menu->children)) ? $menu->children : array();
                            $childCount = count($children);

                            // Transacciones y Ventas: clic directo (sin submenu)
                            $menusDirectos = array('Transacciones', 'Ventas');
                            $esEnlaceDirecto = in_array($menu->menu_nombre, $menusDirectos) && $childCount === 1;
                            $hrefDirecto = $esEnlaceDirecto
                                ? base_url(strtolower($children[0]->menu_controlador))
                                : '#';
                        ?>
                        <?php if ($esEnlaceDirecto) { ?>
                        <li>
                            <a href="<?= $hrefDirecto ?>">
                                <span><i class="<?= $menu->menu_icono ?>"></i></span>
                                <span> <?= $menu->menu_nombre ?> </span>
                            </a>
                        </li>
                        <?php } else { ?>
                        <li class="has-submenu">
                            <a href="#">
                                <span><i class="<?= $menu->menu_icono ?>"></i></span>
                                <span> <?= $menu->menu_nombre ?> </span>
                            </a>
                            <?php if ($childCount > 0) { ?>
                            <ul class="submenu">
                                <?php foreach ($children as $child) { ?>
                                <li><a href="<?= base_url(strtolower($child->menu_controlador)) ?>"><?= $child->menu_nombre ?></a></li>
                                <?php } ?>
                            </ul>
                            <?php } ?>
                        </li>
                        <?php } ?>
                        <?php } ?>
                    </ul>
                </div>
            </div>
            <div class="menu-extras">
                <ul class="nav navbar-nav navbar-right pull-right">
                    <li class="dropdown user-box">
                        <a href="#" class="dropdown-toggle waves-effect waves-light profile " data-toggle="dropdown" aria-expanded="true">
                            <img src="<?=$this->session->userdata('usuario_foto')?>" alt="user-img" class="img-circle user-img">
                            <div class="user-status away"><i class="zmdi zmdi-dot-circle"></i></div>
                        </a>

                        <ul class="dropdown-menu">
                            <li><a href="<?=base_url('perfil')?>"><i class="fa fa-user"></i> Perfil <?=$this->session->userdata('usuario_user');?></a></li>
                            <li><a href="<?=base_url('login/cerrar_sesion')?>"><i class="fa fa-sign-out"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
                <div class="menu-item">
                    <a class="navbar-toggle">
                        <div class="lines">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
<div class="wrapper">
    <div class="container"><br>
