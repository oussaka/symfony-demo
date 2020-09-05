import {render} from 'react-dom';
import React, {useEffect} from 'react';
import {useState, useCallback} from 'react'

function fetchData(url) {
    const [items, setItems] = useState([]);
    const [counts, setCounts] = useState(0);
    const [next, setNext] = useState(null);
    const load = useCallback(async () => {
        const response = await fetch(next || url,{
            headers: {
                'Accept': 'application/ld+json'
            }
        })
        const responseData = await response.json()
        if (response.ok) {
            setItems( items => [...items, ...responseData['hydra:member']]);
            setCounts(responseData['hydra:totalItems'])
            if (responseData['hydra:view'] && responseData['hydra:view']['hydra:next']) {
                setNext(responseData['hydra:view']['hydra:next'])
            } else {
                console.log('no more')
                setNext(null)
            }
        } else {
            console.error(responseData)
        }
    }, [url, next]);

    return {
        items,
        counts,
        load,
        next
    };
}

function Title(props) {
    return <h3>
        <i className="fa fa-comments" aria-hidden="true"></i> {props.counts} Commentaires
    </h3>;
}

function Comment({comment}) {
    console.log('render comment')
    const date = new Date(comment.publishedAt)
    return <div className="row post-comment">
            <h4 className="col-sm-3">
                <strong>{comment.author.fullName}</strong>
                commenté le
                <strong>{date.toLocaleString(undefined, {dateStyle: 'medium', timeStyle: 'short'})}</strong>
            </h4>
            <div className="col-sm-9">
                <p>{comment.content}</p>
            </div>
        </div>
    ;
}

function Comments (props) {
    const {items: comments, counts, load, next} = fetchData('/api/comments?post=' + parseInt(props.post))

    useEffect(() => {
        load()
    }, [])

    return <div>
        <Title counts={counts} />
        {comments && comments.map(c => <Comment key={c.id} comment={c} />)}
        <button onClick={load} className={'btn btn-primary'} disabled={next == null}>Afficher plus de commentaires</button>
    </div>;
}

class PostComments extends HTMLElement {

    connectedCallback() {
        render(<Comments post={this.dataset.post} user={this.dataset.user} />, this)
    }

}

customElements.define('post-comments', PostComments)