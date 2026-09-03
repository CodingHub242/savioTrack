import { Redis } from '@upstash/redis'
import pg from 'pg'

export const handler = async (event, context) => {
  try {
    // 1. Initialize Supabase Postgres (using the Pooler URI)
    const pool = new pg.Pool({
      connectionString: process.env.DATABASE_URL,
      ssl: { rejectUnauthorized: false } // Required for Supabase
    })

    // 2. Initialize Upstash Redis (HTTP client optimized for serverless)
    const redis = new Redis({
      url: process.env.UPSTASH_REDIS_REST_URL,
      token: process.env.UPSTASH_REDIS_REST_TOKEN,
    })

    // Test operations
    await redis.set('netlify_test', 'Hello from Upstash!')
    const cacheVal = await redis.get('netlify_test')
    
    const dbRes = await pool.query('SELECT NOW()')
    await pool.end() // Crucial: Always close connections in serverless functions

    return {
      statusCode: 200,
      body: JSON.stringify({ 
        message: "Successfully connected!",
        redisData: cacheVal,
        dbTime: dbRes.rows[0].now 
      }),
    }
  } catch (error) {
    return { statusCode: 500, body: error.toString() }
  }
}