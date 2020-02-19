<?php
  require "dbConnect.php";
  $db = get_db();
	
	$statement = $db->prepare("SELECT team_name, team_id FROM team");
	$statement->execute();

	$badGet = True;
	while ($row = $statement->fetch(PDO::FETCH_ASSOC))
	{
		$team = $row['team_name'];
		$teamID = $row['team_id'];
		if (ucfirst($_GET['team']) == $team) {
			$badGet = False;
			break;
		}
	}
	if ($badGet)
	{
		echo "bad link";
		die();
	}

	if (isset($_POST['first_name']) && $_POST['first_name'] && ($_POST['form_name'] == "insert"))
	{
		// We just added a player
		$statement = $db->prepare("INSERT INTO player 
		VALUES ($_POST[new_player_id],$_POST[current_team_id],
				'$_POST[first_name]','$_POST[last_name]',
				$_POST[jersey_number],'$_POST[height]',
				'$_POST[position]','$_POST[home]',null,null)");
		$statement->execute();
	}

	if (isset($_POST['delete_player_id']) && ($_POST['form_name'] == "delete"))
	{
		// We are deleting a player
		$statement = $db->prepare("DELETE FROM player WHERE player_id=$_POST[delete_player_id]");
		$statement->execute();
	}
?>

<!DOCTYPE html>

<html lang="en-us">
<head>
	<link rel="icon" href="favicon.ico">
	<meta charset="utf-8">
	<title><?php echo $team?> Roster | Competitive Sports</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="BYUI Women's Competitive Stuff">
	<meta name="author" content="Kevin Matos">
	<link rel="stylesheet" href="main.css">
	<style>
		th {
			border-top: 1px solid #999;
			border-bottom: 1px solid #999;
			color: #333;
			padding: 4px 4px 3px;
			padding-left: 0;
		}
		
		tr {
			border-bottom: 1px solid #ccc;
		}
		
		tr:nth-child(even) {
	    background-color: #f2f2f2;
	  }
	</style>
</head>

<body>
	<script src="null.js"></script>
	<ul class="navbar">
		<li><a href="soon.html">Schedule</a></li>
		<li><a href="soon.html">Stats</a></li>
		<li><a class="active" href="index.php">Teams</a></li>
		<li><a href="soon.html">About</a></li>
	</ul>
	<div class="mainbox">
		<h1><?php echo $team; ?> Roster</h1>
		<h3>Team Roster</h3>
		<table>
			<thead>
				<tr>
					<th width="initial-scale"></th>
					<th>NAME</th>
					<th>POS</th>
					<th>HT</th>
					<th>HOME</th>
				</tr>
			</thead>
			<?php
				$statement = $db->prepare("SELECT player_id, first_name, last_name, jersey_number, position, height, home
					FROM player WHERE team_id=$teamID");
				$statement->execute();
				while ($row = $statement->fetch(PDO::FETCH_ASSOC))
				{
					$id = $row['player_id'];
					$firstName = $row['first_name'];
					$name = $firstName . " " . $row['last_name'];
					$no = $row['jersey_number'];
					$pos = $row['position'];
					$ht = $row['height'];
					$home = $row['home'];
					echo "
					<tr><td>
					<img class='playercrop'";					
					echo "src='headshot/" . $id . "_full.png'";
					//echo "src='https://cdn.onlinewebfonts.com/svg/img_226340.png'";
					echo "
					alt='" . $firstName . "'></td>
					<td><span class='name'>$name </span><span class='number'>$no</span></td>
					<td>$pos</td>
					<td>$ht</td>
					<td>$home</td></tr>
					";
				}
    		?>
		</table>
		<div class="coaches"><p>
			<?php
				$statement = $db->prepare("SELECT first_name, last_name, head_coach
				FROM staff WHERE team_id=$teamID");
				$statement->execute();
				while ($row = $statement->fetch(PDO::FETCH_ASSOC))
				{
					$coachName = $row['first_name'] . " " . $row['last_name'];
					$isHead = $row['head_coach'];
					if ($isHead == 'Y') {
						echo "<b>Coach:</b> $coachName<br>";
					}
					if ($isHead == 'N') {
						echo "<b>Assistant:</b> $coachName<br>";
						}
				}
			?>
		</p></div>
		<?php
			$statement = $db->prepare("SELECT player_id FROM player ORDER BY player_id DESC");
			$statement->execute();
			$row = $statement->fetch(PDO::FETCH_ASSOC);
			$newID = $row['player_id'] + 1;
		?>

		<div>
			<h1>Modify Roster</h1>
			<h3>Add a player</h3><hr>
			<form name="insert" action="/roster.php?team=<?php echo strtolower($team); ?>" method="post">
				<input type="hidden" id="form_name" name="form_name" value="insert">
				<input type="hidden" id="new_player_id" name="new_player_id" value="<?php echo $newID ?>">
				<input type="hidden" id="current_team_id" name="current_team_id" value="<?php echo $teamID ?>">
				<label for="first_name">First name:</label>
  				<input type="text" id="first_name" name="first_name" placeholder="First Name" size="30" required><br>
				<label for="last_name">Last name:</label>
				<input type="text" id="last_name" name="last_name" placeholder="Last Name" size="30" required><br>
				<label for="jersey_number">Jersey number:</label>
				<input type="number" id="jersey_number" maxlength="2" min="0" max="99" name="jersey_number" placeholder="#" size="4" required>
				<label for="position">Position:</label>
				<input type="text" id="position" name="position" maxlength="5" size="6" placeholder="Position" required><br>
				<label for="height">Height:</label>
				<select id="height" name="height" required>
					<option value="" selected disabled>Choose</option>
					<option value="5 1">5' 1"</option>
					<option value="5 2">5' 2"</option>
					<option value="5 3">5' 3"</option>
					<option value="5 4">5' 4"</option>
					<option value="5 5">5' 5"</option>
					<option value="5 6">5' 6"</option>
					<option value="5 7">5' 7"</option>
					<option value="5 8">5' 8"</option>
					<option value="5 9">5' 9"</option>
					<option value="5 10">5' 10"</option>
					<option value="5 11">5' 11"</option>
					<option value="6 0">6' 0"</option>
					<option value="6 1">6' 1"</option>
					<option value="6 2">6' 2"</option>
				</select><br>
				<label for="home">Home (optional):</label>
				<input type="text" id="home" name="home" maxlength="20" size="20" placeholder="optional"><br><br>
				<input type="submit" value="Add player"><br><br>
			</form>
			<h3>Delete a player</h3><hr>
			<form name="delete" action="/roster.php?team=<?php echo strtolower($team); ?>" method="post">
				<input type="hidden" id="form_name" name="form_name" value="delete">
				<select id="delete_player_id" name="delete_player_id">
					<option value="" selected disabled>Choose player</option>
					<?php
						$statement = $db->prepare("SELECT player_id, first_name, last_name
												FROM player WHERE team_id=$teamID");
						$statement->execute();
						while ($row = $statement->fetch(PDO::FETCH_ASSOC))
						{
							$id = $row['player_id'];
							$firstName = $row['first_name'];
							$name = $firstName . " " . $row['last_name'];
							echo "
							<option value=$id>$name</option>
							";
						}
					?>
				</select><br><br>
				<input type="submit" value="Delete player"><br>
			</form>
		</div>
	</div>

</body>

</html>

<?php
	$_POST = array();
?>