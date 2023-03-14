<?php
$dsn = 'pgsql:host=localhost;port=5433;dbname=zadaca-2';
$connection = new PDO($dsn.';user=postgres;password=nina');

if (!isset($_GET['page']) && !isset($_GET['slug'])) {
    $sql_get_sport = "SELECT name, slug FROM Sport";
    $result_get_sport = $connection->query($sql_get_sport);

    if ($result_get_sport->rowCount() !== 0) {
        ?> 
        <table>
        <?php while ($row = $result_get_sport->fetch()) { ?>
            <tr> 
                <td> <?php echo $row["name"] ?> </td>
                <td><a href="/?page=sport&amp;slug=<?php echo $row["slug"] ?>">Detalji</a></td>
            </tr>
        <?php } ?>
        </table>
    <?php 
    } else {
        http_response_code(404);
        echo "404 not found";
    }
} 
elseif (isset($_GET['slug'])) { 
    if ($_GET['slug'] !== '') {
        $slug = $_GET['slug'];

        if ($_GET['page'] === 'sport') {

            $sql_get_tournament = "SELECT tournament.name, tournament.slug
                                    FROM sport JOIN tournament 
                                    ON sport.Id = tournament.sport_id
                                    WHERE sport.slug LIKE '$slug'";
            $result_get_tournament = $connection->query($sql_get_tournament);

            if ($result_get_tournament->rowCount() !== 0) {
                ?>
                <table>
                <?php while ($row = $result_get_tournament->fetch()) { ?>
                    <tr> 
                        <td> <?php echo $row["name"] ?> </td>
                        <td><a href="/?page=tournament&amp;slug=<?php echo $row["slug"] ?>">Detalji</a></td>
                    </tr>
                <?php } ?>
                </table>
            <?php
            } else {
                http_response_code(404);
                echo "404 not found";
            }
        } 
        if ($_GET['page'] === 'tournament') {
            $sql_get_event = "SELECT tournament.name, event.Id
                            FROM tournament JOIN event 
                            ON tournament.Id = event.tournament_id
                            WHERE tournament.slug LIKE '$slug'";
            $result_get_event = $connection->query($sql_get_event);

            if ($result_get_event->rowCount() !== 0) {
                ?>
                <table>
                <?php while ($row = $result_get_event->fetch()) { ?>
                    <tr> 
                        <td>Event <?php echo $row["id"] ?> turnira <?php echo $row["name"] ?> </td>
                        <td><a href="/?page=event&amp;id=<?php echo $row["id"] ?>">Detalji</a></td>
                    </tr>
                <?php } ?>
                </table>
            <?php
            } else {
                http_response_code(404);
                echo "404 not found";
            }
        } 
    } else {
        http_response_code(404);
        echo "404 not found";
    }
} elseif (isset($_GET['id'])) {
    if ($_GET['id'] !== '') {
        $id = $_GET['id'];
        $sql_get_event_details = "SELECT *
                                    FROM event 
                                    WHERE event.Id = $id";
        $result_get_event_details = $connection->query($sql_get_event_details);

        if ($result_get_event_details->rowCount() !== 0) {
            ?>
            <table>
                <tr> 
                    <td>Id</td>
                    <td>external_id</td>
                    <td>home_team_id</td>
                    <td>away_team_id</td>
                    <td>start_date</td>
                    <td>home_score</td>
                    <td>away_score</td>
                    <td>tournament_id</td>
                </tr>
                <?php while ($row = $result_get_event_details->fetch()) { ?>
                    <tr> 
                        <td> <?php echo $row["id"] ?> </td>
                        <td> <?php echo $row["external_id"] ?> </td>
                        <td> <?php echo $row["home_team_id"] ?> </td>
                        <td> <?php echo $row["away_team_id"] ?> </td>
                        <td> <?php echo $row["start_date"] ?> </td>
                        <td> <?php echo $row["home_score"] ?> </td>
                        <td> <?php echo $row["away_score"] ?> </td>
                        <td> <?php echo $row["tournament_id"] ?> </td>
                    </tr>
                <?php } ?>
            </table>
        <?php
        } else {
            http_response_code(404);
            echo "404 not found";
        }
    } else {
        http_response_code(404);
        echo "404 not found";
    }
} else {
    http_response_code(404);
    echo "404 not found";
}