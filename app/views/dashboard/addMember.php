<?php if (empty($user_id)) : ?>

    <div class="controls">
        <?php echo Form::select('user_ids[]', array_except(User::lists('name', 'id'), $users), '', array('multiple', 'style' => 'width:450px;height:300px', 'id' => 'user_ids')) ?>
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
</style>