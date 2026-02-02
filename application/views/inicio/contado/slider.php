<style>
    .embed-container {
        position: relative;
        padding-bottom: 38.25%;
        height: 0;
        overflow: hidden;
    }

    .embed-container iframe {
        position: absolute;
        top:0;
        left: 0;
        width: 100%;
        height: 100%;
    }
</style>

<div class="embed-container">
    <iframe frameborder="0" src="<?php echo $this->config->item('url_wordpress').'/slider'; ?>" scrolling="no"></iframe>
</div>