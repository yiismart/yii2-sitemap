<?php

use yii\helpers\Html;

$loc = $model->loc;
if (preg_match('|^http://|', $loc) == 0)
    $loc = $domain . $loc;
$loc = Html::tag('loc', $loc) . "\n";

$lastmod = Html::tag('lastmod', $model->lastmod) . "\n";

$changefreq = empty($model->changefreq) ? '' : Html::tag('changefreq', $model->changefreq);

$priority = empty($model->priority) ? '' : Html::tag('priority', $model->priority);

?>
<url>
<?= $loc; ?>
<?= $lastmod; ?>
<?= $changefreq; ?>
<?= $priority; ?>
</url>
