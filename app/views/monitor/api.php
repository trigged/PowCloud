<?php echo $header; ?>
    <div class="monitor">
        <?php foreach ($apiData as $tableName => $apiInfo): ?>
            <?php $time = 0;
            $count = 0;
            $methodAll = array('rgb(74,228,43) ' => 'index', 'rgb(28,171,251)' => 'show', 'rgb(245,191,0)' => 'edit', 'rgb(245,42,42)' => 'delete'); ?>
            <div class="<?php echo $tableName; ?> monitor-row row ">
                <div class="col-md-2 default box" style="margin:0 auto">
                    <div class="table-name"><span><?php echo ucfirst($tableName); ?></span></div>
                </div>
                <?php foreach ($methodAll as $color => $subMethod) : ?>
                    <div class="col-md-2 <?php echo $subMethod ?> box"
                         style="margin:0 auto; background: <?php echo $color ?>; ">
                        <span class="method-name"><?php echo ucfirst($subMethod) ?></span>
                        <?php foreach ($apiInfo as $method => $typeInfo): ?>
                            <?php if ($method == $subMethod): ?>
                                <?php foreach ($typeInfo as $type => $value): ?>
                                    <?php if ($type == 'time'): ?>
                                        <?php $time += $value; ?>
                                        <div class="time" style="border-bottom: 1px solid #0d638f">
                                            <span class="monitor-data"><?php echo $value ?>（毫秒）</span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($type == 'count'): ?>
                                        <?php $count += $value; ?>
                                        <div class="count">
                                            <span class="monitor-data"><?php echo $value ?>(次)</span>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
                <div class="col-md-2 Total box default" style="margin:0 auto;">
                    <span class="method-name">Total</span>

                    <div class="time" style="border-bottom: 1px solid #0d638f">
                        <span class="monitor-data"><?php echo $time ?>（毫秒）</span>
                    </div>
                    <div class="count">
                        <span class="monitor-data"><?php echo $count ?>(次)</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php echo $footer; ?>