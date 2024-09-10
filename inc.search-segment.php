<?php /* ?>
<div class="filters">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <form action="<?php echo ROOT_DIR.$location_slug; ?>search" method="GET">
                    <div class="row">
                        <div class="col-md-9 mt-3">
                            <input name="k" type="text" class="form-control" id="searchField1" value="<?php if (isset($_GET["k"])) echo trim($_GET["k"]); ?>" placeholder="Keywords">
                        </div>
                        <div class="col-md-3 mt-3">
                            <button type="submit" class="btn btn-dark btn-block">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php */ ?>

<?php /*if (!$advanced_search) { ?> 
    <div class="filters">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form action="<?php echo ROOT_DIR.$location_slug; ?>search" method="GET">
                        <div class="row">
                            <div class="col-md-3 mt-3">
                                <input name="k" type="text" class="form-control" id="searchField2" value="<?php if (isset($_GET["k"])) echo trim($_GET["k"]); ?>" placeholder="Keywords">
                            </div>

                            <div class="col-md-3 mt-3">
                                <select name="p_cat" class="form-control">
                                    <option value="">Programmes Category</option>
                                    <option disabled> - - - - - - - </option>
                                    <?php 
                                        mysqli_select_db($config, $database_config);
                                        $query_valuesList = "SELECT * 
                                                            FROM options
                                                            WHERE option_name = 'programme subjects' 
                                                                AND status = 'active' 
                                                            ORDER BY option_sort DESC, option_id ASC";
                                        $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                        $row_valuesList = mysqli_fetch_assoc($valuesList);
                                        $totalRows_valuesList = mysqli_num_rows($valuesList);
                                    ?>

                                    <?php if ($totalRows_valuesList) do { ?>
                                        <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_cat'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                            <?php echo ucwords($row_valuesList['option_value']) ?>
                                        </option>
                                    <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                </select>
                            </div>

                            <div class="col-md-3 mt-3">
                                <select name="p_type" class="form-control">
                                    <option value="">Programme Type</option>
                                    <option disabled> - - - - - - - </option>
                                    <?php 
                                        mysqli_select_db($config, $database_config);
                                        $query_valuesList = "SELECT * 
                                                            FROM options
                                                            WHERE option_name = 'programme type' 
                                                                AND status = 'active' 
                                                            ORDER BY option_sort DESC, option_id ASC";
                                        $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                        $row_valuesList = mysqli_fetch_assoc($valuesList);
                                        $totalRows_valuesList = mysqli_num_rows($valuesList);
                                    ?>

                                    <?php if ($totalRows_valuesList) do { ?>
                                        <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_type'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                            <?php echo ucwords($row_valuesList['option_value']) ?>
                                        </option>
                                    <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                </select>
                            </div>

                            <!-- <div class="col-md-3 mt-3">
                                <select name="p_study" class="form-control">
                                    <option value="">Study Type</option>
                                    <option disabled> - - - - - - - </option>
                                    <?php 
                                        mysqli_select_db($config, $database_config);
                                        $query_valuesList = "SELECT * 
                                                            FROM options
                                                            WHERE option_name = 'lecture format' 
                                                                AND status = 'active' 
                                                            ORDER BY option_sort DESC, option_id ASC";
                                        $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                        $row_valuesList = mysqli_fetch_assoc($valuesList);
                                        $totalRows_valuesList = mysqli_num_rows($valuesList);
                                    ?>

                                    <?php if ($totalRows_valuesList) do { ?>
                                        <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_study'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                            <?php echo ucwords($row_valuesList['option_value']) ?>
                                        </option>
                                    <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                </select>
                            </div> -->
                            <div class="col-md-3 mt-3">
                                <button type="submit" class="btn btn-dark btn-block">Search</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?> 
    <div class="filter">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <form action="<?php echo ROOT_DIR.$location_slug; ?>search" method="GET">
                        <div class="row">
                            <div class="col-md-3 mt-3">
                                <input name="k" type="text" class="form-control" id="searchField3" value="<?php if (isset($_GET["k"])) echo trim($_GET["k"]); ?>" placeholder="Keywords">
                            </div>

                            <div class="col-md-3 mt-3">
                                <select name="p_cat" class="form-control">
                                    <option value="">Programmes Category</option>
                                    <option disabled> - - - - - - - </option>
                                    <?php 
                                        mysqli_select_db($config, $database_config);
                                        $query_valuesList = "SELECT * 
                                                            FROM options
                                                            WHERE option_name = 'programme subjects' 
                                                                AND status = 'active' 
                                                            ORDER BY option_sort DESC, option_id ASC";
                                        $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                        $row_valuesList = mysqli_fetch_assoc($valuesList);
                                        $totalRows_valuesList = mysqli_num_rows($valuesList);
                                    ?>

                                    <?php if ($totalRows_valuesList) do { ?>
                                        <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_cat'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                            <?php echo ucwords($row_valuesList['option_value']) ?>
                                        </option>
                                    <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                </select>
                            </div>

                            <div class="col-md-3 mt-3">
                                <select name="p_type" class="form-control">
                                    <option value="">Programme Type</option>
                                    <option disabled> - - - - - - - </option>
                                    <?php 
                                        mysqli_select_db($config, $database_config);
                                        $query_valuesList = "SELECT * 
                                                            FROM options
                                                            WHERE option_name = 'programme type' 
                                                                AND status = 'active' 
                                                            ORDER BY option_sort DESC, option_id ASC";
                                        $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                        $row_valuesList = mysqli_fetch_assoc($valuesList);
                                        $totalRows_valuesList = mysqli_num_rows($valuesList);
                                    ?>

                                    <?php if ($totalRows_valuesList) do { ?>
                                        <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_type'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                            <?php echo ucwords($row_valuesList['option_value']) ?>
                                        </option>
                                    <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                </select>
                            </div>

                            <div class="col-md-3 mt-3">
                                <select name="p_study" class="form-control">
                                    <option value="">Study Type</option>
                                    <option disabled> - - - - - - - </option>
                                    <?php 
                                        mysqli_select_db($config, $database_config);
                                        $query_valuesList = "SELECT * 
                                                            FROM options
                                                            WHERE option_name = 'lecture format' 
                                                                AND status = 'active' 
                                                            ORDER BY option_sort DESC, option_id ASC";
                                        $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                        $row_valuesList = mysqli_fetch_assoc($valuesList);
                                        $totalRows_valuesList = mysqli_num_rows($valuesList);
                                    ?>

                                    <?php if ($totalRows_valuesList) do { ?>
                                        <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_study'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                            <?php echo ucwords($row_valuesList['option_value']) ?>
                                        </option>
                                    <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <div class="collapse" id="collapseFilter">
                                    <div class="row">
                                        <div class="col-md-3 mt-3">
                                            <select name="p_diff" class="form-control">
                                                <option value="">Difficulty</option>
                                                <option disabled> - - - - - - - </option>
                                                <?php 
                                                    mysqli_select_db($config, $database_config);
                                                    $query_valuesList = "SELECT * 
                                                                        FROM options
                                                                        WHERE option_name = 'programme difficulty' 
                                                                            AND status = 'active' 
                                                                        ORDER BY option_sort DESC, option_id ASC";
                                                    $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                                    $row_valuesList = mysqli_fetch_assoc($valuesList);
                                                    $totalRows_valuesList = mysqli_num_rows($valuesList);
                                                ?>

                                                <?php if ($totalRows_valuesList) do { ?>
                                                    <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_diff'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                                        <?php echo ucwords($row_valuesList['option_value']) ?>
                                                    </option>
                                                <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 mt-3">
                                            <select name="p_creds" class="form-control">
                                                <option value="">Credits</option>
                                                <option disabled> - - - - - - - </option>
                                                <?php 
                                                    mysqli_select_db($config, $database_config);
                                                    $query_valuesList = "SELECT * 
                                                                        FROM options
                                                                        WHERE option_name = 'programme credits' 
                                                                            AND status = 'active' 
                                                                        ORDER BY option_sort DESC, option_id ASC";
                                                    $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                                    $row_valuesList = mysqli_fetch_assoc($valuesList);
                                                    $totalRows_valuesList = mysqli_num_rows($valuesList);
                                                ?>

                                                <?php if ($totalRows_valuesList) do { ?>
                                                    <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_creds'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                                        <?php echo ucwords($row_valuesList['option_value']) ?>
                                                    </option>
                                                <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 mt-3">
                                            <select name="p_dur" class="form-control">
                                                <option value="">Duration</option>
                                                <option disabled> - - - - - - - </option>
                                                <?php 
                                                    mysqli_select_db($config, $database_config);
                                                    $query_valuesList = "SELECT * 
                                                                        FROM options
                                                                        WHERE option_name = 'programme duration' 
                                                                            AND status = 'active' 
                                                                        ORDER BY option_sort DESC, option_id ASC";
                                                    $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                                    $row_valuesList = mysqli_fetch_assoc($valuesList);
                                                    $totalRows_valuesList = mysqli_num_rows($valuesList);
                                                ?>

                                                <?php if ($totalRows_valuesList) do { ?>
                                                    <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_dur'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                                        <?php echo ucwords($row_valuesList['option_value']) ?>
                                                    </option>
                                                <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                            </select>
                                        </div>

                                        <div class="col-md-3 mt-3">
                                            <select name="p_effort" class="form-control">
                                                <option value="">Effort</option>
                                                <option disabled> - - - - - - - </option>
                                                <?php 
                                                    mysqli_select_db($config, $database_config);
                                                    $query_valuesList = "SELECT * 
                                                                        FROM options
                                                                        WHERE option_name = 'programme effort' 
                                                                            AND status = 'active' 
                                                                        ORDER BY option_sort DESC, option_id ASC";
                                                    $valuesList = mysqli_query($config, $query_valuesList) or die(mysqli_error($config));
                                                    $row_valuesList = mysqli_fetch_assoc($valuesList);
                                                    $totalRows_valuesList = mysqli_num_rows($valuesList);
                                                ?>

                                                <?php if ($totalRows_valuesList) do { ?>
                                                    <option value="<?php echo $row_valuesList['option_id'] ?>" <?php if ($_GET['p_effort'] == $row_valuesList['option_id']) echo ' selected="selected"' ?>>
                                                        <?php echo ucwords($row_valuesList['option_value']) ?> per week
                                                    </option>
                                                <?php } while($row_valuesList = mysqli_fetch_assoc($valuesList));?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3"></div>
                            
                            <div class="col-md-3 mt-3">
                                <p class="text-center serviceList">
                                    <a data-toggle="collapse" href="#collapseFilter" role="button" aria-expanded="false" aria-controls="collapseFilter">
                                        Advanced Filter <i class="fa fa-chevron-down"></i>
                                        <i class="fa fa-chevron-up"></i>
                                    </a>
                                </p>
                            </div>
                            <div class="col-md-3 mt-2">
                                <button type="submit" class="btn btn-dark btn-block">Search</button>
                            </div>
                            <div class="col-md-3"></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php }*/ ?> 