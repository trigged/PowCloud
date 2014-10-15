<?php if (empty($user_id)) : ?>

    <div class="jumbotron">
        <h2>邀请小伙伴</h2>

        <form action="<?php echo URL::action('LoginController@registerUser'); ?>" method="post">
            <p/>

            <p>
                <input class="feedback-input" name="email" id="email" placeholder="小伙伴的邮箱地址"/>
            </p>
        </form>

        <p>
            <a class="btn btn-primary btn-lg">
                Learn more
            </a>
        </p>
    </div>

<?php else : ?>

    <div class="controls member-item">
        <?php if (Auth::user()->id == $user_id): ?>
            <span>（ 你 ）</span>

        <?php endif; ?>
        <?php echo User::find($user_id)->name; ?>
    </div>


<?php endif; ?>

<style>
    .member-item {
        margin: 0 auto;
        width: 50%
    }

    .feedback-input {
        color: #3c3c3c;
        font-family: Helvetica, Arial, sans-serif;
        font-weight: 500;
        font-size: 18px;
        border-radius: 0;
        line-height: 22px;
        background-color: #fbfbfb;
        padding: 13px 13px 13px 54px;
        margin-bottom: 10px;
        width: 100%;
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        -ms-box-sizing: border-box;
        box-sizing: border-box;
        border: 3px solid #000000;
    }
</style
