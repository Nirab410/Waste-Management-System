INSERT INTO recycling_centers (name, zone, max_capacity)
VALUES ('Eco Center A', 'Zone-A', 1000);

INSERT INTO waste_requests (resident_id, center_id, waste_type_id, estimated_weight, frequency)
VALUES (2, 1, 1, 5.5, 'ONE_TIME');

INSERT INTO collector_assignments (request_id, collector_id)
VALUES (1, 3);

INSERT INTO chat_messages (request_id, sender_id, message)
VALUES
(1, 2, 'Please come after 5 PM'),
(1, 3, 'Okay, I am on the way');

INSERT INTO feedback (request_id, rating, comment)
VALUES (1, 5, 'Very professional service');
