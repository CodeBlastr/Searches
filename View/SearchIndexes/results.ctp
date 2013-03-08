<?php echo $this->Element('google_form', array('plugin'=>'Searchable')) ?>
<?php //if(!empty($search_results['spelling'])){ ?>
<p>
    Did you mean <a href="/searchable/search_indexes/results/?term=<?php //echo $search_results['spelling']?>"><?php //echo $search_results['spelling']?></a>?
</p>
<?php //} ?>


<div>

<p>
<?php //echo $search_results['result_text'] ?>
</p>

<?php
foreach($search_results as $result)
{
?>
<div>
    <h3><a href="<?php echo $result->link ?>"><?php echo $result->title ?></a></h3>
    <p>
        <?php echo $result->htmlSnippet ?><br />
        <a href="<?php echo $result->link ?>"><?php echo $result->link ?></a>
    </p>
</div>
<?php
}
?>

<p><?php //echo $search_results['paging']?></p>
</div>