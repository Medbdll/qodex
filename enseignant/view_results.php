          <?php
            require_once '../config/database.php';

            $stmt = $pdo->query("
    SELECT 
        r.id,
        r.quiz_id,
        q.titre AS quiz_title,
        r.etudiant_id,
        u.nom AS etudiant_nom,
        r.score,
        r.total_questions,
        r.completed_at
    FROM results r
    LEFT JOIN quizzes q ON r.quiz_id = q.id
    LEFT JOIN users u ON r.etudiant_id = u.id
    ORDER BY r.completed_at DESC
");
            $results = $stmt->fetchAll();
            ?>
          <div id="results" class="section-content hidden">

              <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                  <h1 class="text-3xl font-bold text-gray-800 mb-6 mt-20">Résultats des Quiz</h1>

                  <div class=" bg-white rounded-lg shadow overflow-hidden">
                      <table class="min-w-full divide-y divide-gray-200">
                          <thead class="bg-gray-50">
                              <tr>
                                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">ID</th>
                                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Quiz</th>
                                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Etudiant</th>
                                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Score</th>
                                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Total</th>
                                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500">Complété le</th>
                              </tr>
                          </thead>
                          <tbody class="bg-white divide-y divide-gray-200">
                              <?php foreach ($results as $r): ?>
                                  <tr>
                                      <td class="px-6 py-4 text-sm text-gray-900"><?= $r['id'] ?></td>
                                      <td class="px-6 py-4 text-sm text-indigo-600 font-semibold"><?= $r['quiz_title'] ?></td>
                                      <td class="px-6 py-4 text-sm text-gray-900"><?= $r['etudiant_nom'] ?></td>
                                      <td class="px-6 py-4 text-sm text-green-700 font-bold"><?= $r['score'] ?></td>
                                      <td class="px-6 py-4 text-sm text-gray-700"><?= $r['total_questions'] ?></td>
                                      <td class="px-6 py-4 text-sm text-gray-500"><?= $r['completed_at'] ?></td>
                                  </tr>
                              <?php endforeach ?>
                          </tbody>
                      </table>
                  </div>
              </div>


          </div>