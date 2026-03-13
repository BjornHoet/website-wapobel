<?php
$prefix = '../';
$activeDagboek = '';
$activePost = 'active';
$activeRekening = '';
$hoofdpostId = $_GET["hoofdpostId"];

include $prefix.'/bin/init.php';
$pageTitle = 'Wijzig posten';
$hoofdPostData = getHoofdPostData($hoofdpostId);

if (loggedIn() === false) {
    header("Location: ".$prefix."bin/login");
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include $prefix.'includes/head.php'; ?>
<body id="page-top">
<div id="wrapper">

    <?php include $prefix.'includes/sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">
            <?php include $prefix.'includes/topbar.php'; ?>

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800"><?php echo $pageTitle; ?></h1>
                </div>

                <!-- Hoofdpost Card -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="font-weight-bold text-primary mr-2"><?php echo $hoofdPostData['referentie']; ?></span>
                            <span class="text-dark"><?php echo $hoofdPostData['omschrijving']; ?></span>
                        </div>
                        <button class="btn btn-success btn-sm" 
                                onclick="toevoegenPost('<?php echo $hoofdPostData['hoofdpostId']; ?>',
                                                       '<?php echo $hoofdPostData['referentie'] ?>',
                                                       '<?php echo $hoofdPostData['omschrijving'] ?>',
                                                       '<?php echo $hoofdPostData['andere'] ?>')"
                                data-toggle="modal" data-target="#postModal">
                            <?php echo ($hoofdPostData['andere'] == 1) ? 'Post/Subpost toevoegen' : 'Subpost toevoegen'; ?>
                        </button>
                    </div>

                    <div class="card-body">
                        <form id="wijzigPostForm" action="<?php echo $prefix ?>bin/pages/changePosten.php" method="post">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-bordered">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th style="width:5%">Ref</th>
                                        <th>Omschrijving</th>
                                        <th style="width:10%">Begroting</th>
                                        <th style="width:5%">Actief</th>
                                        <th style="width:5%">Verwijderen</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $posten = getPosten($wateringData['wateringId'], $wateringJaar, $hoofdpostId);
                                    $rowId = 0;
                                    foreach ($posten as $post):
                                        $subPosten = getSubPosten($wateringData['wateringId'], $wateringJaar, $post['postId']);
                                        $subPostThere = !empty($subPosten);
                                        $rowId++;
                                        $postClass = ($post['actief'] === 'X') ? '' : 'table-row-inactive';
                                    ?>
                                        <input type="hidden" name="p_ref_<?php echo $post['postId']; ?>" value="<?php echo $post['referentie']; ?>">
                                        <input type="hidden" name="p_oms_<?php echo $post['postId']; ?>" value="<?php echo $post['omschrijving']; ?>">
                                        <input type="hidden" name="p_ram_<?php echo $post['postId']; ?>" value="<?php echo $post['raming']; ?>">
                                        <tr class="<?php echo $postClass; ?>" data-row-id="<?php echo $rowId; ?>">
                                            <td contenteditable="<?php echo $hoofdPostData['andere'] ? 'true' : 'false'; ?>"><?php echo $post['referentie']; ?></td>
                                            <td contenteditable="<?php echo $hoofdPostData['andere'] ? 'true' : 'false'; ?>"><?php echo $post['omschrijving']; ?></td>
                                            <td><?php if(!$subPostThere) echo $post['raming']; ?></td>
                                            <td>
                                                <input type="checkbox" <?php echo ($post['actief']==='X')?'checked':''; ?> data-toggle="toggle" data-on="I" data-off="O" data-size="xs">
                                            </td>
                                            <td></td>
                                        </tr>

                                        <!-- Subposten -->
                                        <?php foreach($subPosten as $subPost):
                                            $rowId++;
                                            $subClass = ($subPost['actief'] === 'X') ? '' : 'table-row-inactive';
                                        ?>
                                        <tr class="small <?php echo $subClass; ?>" data-row-id="<?php echo $rowId; ?>">
                                            <td class="pl-4" contenteditable="true"><?php echo $subPost['referentie']; ?></td>
                                            <td class="pl-4" contenteditable="true"><?php echo $subPost['omschrijving']; ?></td>
                                            <td><?php echo $subPost['raming']; ?></td>
                                            <td>
                                                <input type="checkbox" <?php echo ($subPost['actief']==='X')?'checked':''; ?> data-toggle="toggle" data-on="I" data-off="O" data-size="xs">
                                            </td>
                                            <td>
                                                <a href="#" onclick="verwijderenPost('<?php echo $subPost['subpostId']; ?>')" class="text-danger">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">Opslaan</button>
                                <a href="../posten/" class="btn btn-secondary">Annuleren</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include $prefix.'includes/footer.php'; ?>
    </div>
</div>

<!-- Modals -->
<?php include $prefix.'includes/modals.php'; ?>

<!-- Scripts -->
<?php include $prefix.'includes/scripts.php'; ?>

<script>
function toevoegenPost(hoofdpostId, referentie, omschrijving, andere) {
    $("#toevoegenPostHoofdPost").text(referentie + ' ' + omschrijving);
    $("#inputHoofdpostId").val(hoofdpostId);
    if(other == '1') {
        $("#inputTypePost").val('P').trigger('change');
        $("#typePost").show();
        $("#addPost").hide();
    } else {
        $("#inputTypePost").val('S').trigger('change');
        $("#typePost").hide();
        $("#addPost").show();
    }
}

// AJAX form submission
$("#addPostForm").submit(function(e){
    e.preventDefault();
    $.post($(this).attr('action'), $(this).serialize(), function(){
        location.reload();
    });
});

function verwijderenPost(subpostId){
    $.post('../bin/pages/deletePost.php', {subpostId: subpostId}, function(){
        location.reload();
    });
}
</script>

<style>
.table-row-inactive {
    color: #b0b5ba !important;
    transition: opacity 0.5s ease;
}
</style>

</body>
</html>
