</div>

<script type="text/javascript" src="modulos/web/js/rel_report_comercial.js"></script>

<?php if (isset($this->view->status) && !$this->view->status && !empty($this->view->campos)) : ?>
    <script type="text/javascript">
        showFormErros(<?php echo json_encode($this->view->campos) ?>);
    </script>
<?php endif; ?>