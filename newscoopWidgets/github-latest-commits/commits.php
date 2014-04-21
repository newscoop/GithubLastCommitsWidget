<style>
.commits-list {
    padding: 0 2px;
}

.commits-list li {
    line-height: 1.0em;
    padding: 6px 8px;
    border-bottom: solid 1px #e6e6e6;
}

.commit-message, .committer-name {
    font-weight: bold;
}

.commit-message a, .commit-meta {
    font-size: 12px;
}

.left {
    float: left;
    padding-right: 10px;
}

.right {
    line-height: 15px;
}
</style>
<ul class="commits-list">
    <?php foreach($this->commits as $commit) { ?>
      <li>
        <div class="left">
          <img src="https://www.gravatar.com/avatar/<?php echo $commit['author']['gravatar_id'] ?>.png?s=38&amp;r=pg&amp;d=identicon">
        </div>
        <div class="right">
          <span class="commit-message"><a href="<?php echo $commit['html_url'] ?>" target="_blank"><?php echo $commit['commit']['message'] ?></a></span><br>
          <span class="commit-meta">by <span class="committer-name"><?php echo $commit['committer']['login'] ?></span> - <span class="commit-time"><span class="date" title="April 20, 2014 9:24pm"><?php echo $commit['commit']['author']['date_diff'] ?></span></span></span>
        </div>
      </li>
    <?php } ?>
</ul>