<?php
session_start();

require_once 'db_connect.php';

try {
    global $pdo;

    $reviewsPerPage = 20;
    $page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $reviewsPerPage;

    $countStmt = $pdo->query('SELECT COUNT(*) FROM reviews');
    $totalReviews = (int)$countStmt->fetchColumn();
    $totalPages = ceil($totalReviews / $reviewsPerPage);

    $stmt = $pdo->prepare('SELECT user_id, order_id, review_text, images, rating FROM reviews LIMIT :limit OFFSET :offset');
    $stmt->bindValue(':limit', $reviewsPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $reviewsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $reviews = [];

    foreach ($reviewsData as $review) {
        $userStmt = $pdo->prepare('SELECT name, email FROM clients WHERE id = :user_id');
        $userStmt->execute([':user_id' => $review['user_id']]);
        $user = $userStmt->fetch(PDO::FETCH_ASSOC);

        $images = json_decode($review['images'], true);
        if (!is_array($images)) {
            $images = [];
        }

        $reviews[] = [
            'user_id' => $review['user_id'],
            'order_id' => $review['order_id'],
            'user_name' => $user['name'] ?? 'Nezināms',
            'user_email' => $user['email'] ?? 'Nezināms',
            'review_text' => $review['review_text'],
            'images' => $images,
            'rating' => floatval($review['rating']),
            'created_at' => '',
        ];
    }
} catch (Exception $e) {
    $reviews = [];
    $totalPages = 1;
    $page = 1;
}

function renderStars($rating) {
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    $html = '';
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fas fa-star" style="color:#f5c518;"></i>';
    }
    if ($halfStar) {
        $html .= '<i class="fas fa-star-half-alt" style="color:#f5c518;"></i>';
    }
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="far fa-star" style="color:#f5c518;"></i>';
    }
    return $html;
}

include 'header.php';
?>

<main class="page-wrapper" style="padding: 20px 10px; max-width: 700px; margin: auto;">
    <h1 class="heading large-h1" style="text-align:center; margin-bottom: 10px; font-size: 1.8em;">Atsauksmes no mūsu klientiem</h1>

    <?php
    $totalRating = 0;
    $count = count($reviews);
    if ($count > 0) {
        foreach ($reviews as $review) {
            $totalRating += $review['rating'];
        }
        $averageRating = $totalRating / $count;
    } else {
        $averageRating = 0;
    }
    ?>

    <div class="overall-rating" style="text-align: center; margin-bottom: 30px; font-size: 1.2em; font-weight: 600;">
        Kopvērtējums: 
        <span style="color: #f5c518;">
            <?php echo renderStars($averageRating); ?>
        </span>
        <span style="font-size: 1em; color: #333; margin-left: 8px;">
            (<?php echo number_format($averageRating, 2); ?> / 5)
        </span>
    </div>

    <?php if (empty($reviews)): ?>
        <p style="text-align:center; font-size: 0.9em;">Nav pieejamas nevienas atsauksmes.</p>
    <?php else: ?>
        <div class="reviews-list" style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($reviews as $review): ?>
                <div class="review-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 12px; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">
                    <div class="review-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                        <div class="review-user" style="font-weight: 600; font-size: 1em;">
                            <?php echo htmlspecialchars($review['user_name']); ?>
                        </div>
                        <div class="review-rating" style="font-size: 1em;">
                            <?php echo renderStars($review['rating']); ?>
                        </div>
                    </div>
                    <div class="review-text" style="font-size: 0.9em; line-height: 1.3; margin-bottom: 10px;">
                        <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                    </div>
                    <?php if (!empty($review['images'])): ?>
                        <div class="review-images" style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <?php foreach ($review['images'] as $img): ?>
                                <img src="<?php echo htmlspecialchars($img); ?>" alt="Review Image" style="max-width: 80px; max-height: 80px; object-fit: cover; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.1); cursor: pointer;">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($totalPages > 1): ?>
        <div class="pagination" style="text-align: center; margin-top: 20px; font-size: 1em;">
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <?php if ($p == $page): ?>
                    <span style="margin: 0 5px; font-weight: bold; text-decoration: underline;"><?php echo $p; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $p; ?>" style="margin: 0 5px; color: #007bff; text-decoration: none;"><?php echo $p; ?></a>
                <?php endif; ?>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</main>

<!-- Modal lai apskatītu attēlus-->
<div id="imageModal" style="display:none; position:fixed; z-index:10000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.8);">
  <span id="modalClose" style="position:absolute; top:20px; right:35px; color:#fff; font-size:40px; font-weight:bold; cursor:pointer;">&times;</span>
  <img id="modalImage" style="margin:auto; display:block; max-width:90%; max-height:90%; border-radius: 10px; box-shadow: 0 0 15px #000;">
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const modalClose = document.getElementById('modalClose');
    const thumbs = document.querySelectorAll('.review-images img');

    thumbs.forEach(function(thumb) {
      thumb.addEventListener('click', function() {
        modal.style.display = 'block';
        modalImg.src = this.src;
      });
    });

    modalClose.addEventListener('click', function() {
      modal.style.display = 'none';
      modalImg.src = '';
    });

    modal.addEventListener('click', function(e) {
      if (e.target === modal) {
        modal.style.display = 'none';
        modalImg.src = '';
      }
    });
  });
</script>

<?php include 'footer.php'; ?>