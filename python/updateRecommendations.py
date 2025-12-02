#Requires: mysql-connector-python. Can be installed via pip if not already available.
# pip install mysql-connector-python in a console

import mysql.connector
from datetime import datetime
from typing import List

def update_recommendation(my_predictions: List[float], user_id: int) -> int:
    """
    Connects to the database and updates user recommendations in a batch.

    Args:
        my_predictions: A list of floats where the index (i ) is the business_id
                        and the value is the recommendation score (rec_score).
        user_id: The ID of the user for whom predictions are being updated.

    Returns:
        The total number of rows inserted or updated.
    """
    
    # --- Database Configuration (UPDATE THESE VALUES) ---
    DB_NAME = 'aiXX'
    USER = 'aiXX'
    PASSWORD = 'aiXX_password'
    HOST = 'localhost'
    PORT = 3306

    # The SQL query remains the same (note the use of %s for placeholders)
    query = """
    INSERT INTO recs (user_id, business_id, rec_score, time) 
    VALUES(%s, %s, %s, NOW()) 
    ON DUPLICATE KEY UPDATE rec_score=VALUES(rec_score)
    """

    # --- 1. Prepare the Data Batch ---
    data_to_insert = []
    
    # my_predictions is a list of scores (floats)
    # The index (i) corresponds to the business ID 
    for i, rec_score in enumerate(my_predictions):
        business_id = i 
        # The tuple order must match the VALUES in the query: (user_id, business_id, rec_score)
        data_to_insert.append((user_id, business_id, rec_score))
        
    total_updated_rows = 0

    # --- 2. Database Connection and Execution (Using Context Managers) ---
    try:
        # Connect to the database
        conn = mysql.connector.connect(
            host=HOST,
            port=PORT,
            database=DB_NAME,
            user=USER,
            password=PASSWORD,
            autocommit=False # Equivalent to con.setAutoCommit(false)
        )
        
        # Use a 'with' block for the cursor; it handles close() automatically.
        with conn.cursor() as cursor:
            
            # Equivalent to st.addBatch() and st.executeBatch()
            cursor.executemany(query, data_to_insert)
            
            # Commit the transaction (Equivalent to con.commit())
            conn.commit()
            
            # The rowcount property holds the number of rows affected by the last operation.
            # For ON DUPLICATE KEY UPDATE, this returns 1 for a new insert and 2 for an update.
            # We return the total number of operations performed.
            total_updated_rows = cursor.rowcount // 2 + (cursor.rowcount % 2) # Rough estimate of records processed
            
            # A more accurate return is simply the length of the batch:
            return len(data_to_insert)

    except mysql.connector.Error as err:
        print(f"Database error: {err}")
        # Rollback the transaction if an error occurred
        if 'conn' in locals() and conn.is_connected():
            conn.rollback()
        return 0

    finally:
        # The 'with' block handles cursor close. This handles connection close.
        if 'conn' in locals() and conn.is_connected():
            conn.close()

# --- Example of running the function ---
if __name__ == '__main__':
    # Sample prediction data:
    # Movie ID 1 has score 4.5, Movie ID 2 has score 3.2, etc.
    user_predictions = [4.5, 3.2, 5.0, 1.1, 4.0] 
    target_user = 101

    num_updated = update_recommendation(user_predictions, target_user)

    print(f"\nRecommendation update completed.")
    print(f"Total prediction records sent to database: {num_updated}")
