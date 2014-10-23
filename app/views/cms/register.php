<?php echo $header; ?>

    <body>

    <div id="container">
        <div class="jumbotron">
            <h1>新用户注册</h1>

            <p></p>

            <div id="form-div">
                <?php echo \Utils\FormBuilderHelper::begin(); //注册表单JS ?>
                <form class="form-horizontal" method="post" id="register">
                    <!--        <form action="-->
                    <?php //echo URL::action('LoginController@registerUser'); ?><!--" method="post">-->
                    <fieldset>
                        <p class="name"> 用户名：
                            <input name="username" type="text" class="feedback-input" placeholder="您的江湖名称" id="name"/>
                        </p>

                        <p class="password"> 密码:
                            <input name="password" type="password" class="feedback-input" id="password"
                                   placeholder="您的江湖暗号"/>
                        </p>

                        <p class="password"> 邮箱地址:
                            <input name="email" type="email"   <?php if (!empty($email)) {
                                echo 'readonly';
                            } ?> class="feedback-input" id="email" placeholder="您的邮箱地址" value="<?php echo $email ?>"/>
                        </p>

                        <p class="password"> 电话号码:
                            <input name="tel" type="tel" class="feedback-input" id="tel" placeholder="您的电话号码"/>
                        </p>


                        <input name="msg_id" type="hidden" value="<?php echo $msg_id ?>" "/>
                        <?php echo \Utils\FormBuilderHelper::staticEnd('register',
                            array( //                                'username' => array('required' => true),
//                                'email'    => array('required' => true),
//                                'password' => array('required' => true),
                            ),
                            URL::action('LoginController@registerUser'),
                            'POST'
                        );//注册表单JS
                        ?>
                        <p>
                            <button id="JS_Sub" class="btn btn-primary btn-lg">杨帆 起航</button>
                        </p>

                    </fieldset>
                </form>
            </div>
        </div>

    </body>
<?php echo $footer; ?>