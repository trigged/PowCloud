<?php $presenter = new Ext\Pagination\ExtBootstrapPresenter($paginator); ?>
<?php if ($paginator->getLastPage() > 1): ?>
    <div>
        <ul class="pagination pagination-lg">
            <?php echo $presenter->render(); ?>
        </ul>
    </div>
<?php endif; ?>