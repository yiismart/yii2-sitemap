<?php

$domain = Yii::$app->getRequest()->getHostInfo();

$pagination = $dataProvider->getPagination();
$pagination->totalCount = $dataProvider->getTotalCount();
$pageCount = $pagination->getPageCount();

echo '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;

?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php for ($i = 0; $i < $pageCount; $i++) {
    $pagination->setPage($i);
    $dataProvider->prepare(true);

    foreach ($dataProvider->getModels() as $model) {
        echo $this->render('url', [
            'model' => $model,
            'domain' => $domain,
        ]);
    }
} ?>
</urlset>
